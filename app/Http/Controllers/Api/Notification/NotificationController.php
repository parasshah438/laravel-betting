<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get user notifications
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:bet_result,promotion,system,deposit,withdrawal',
            'is_read' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $perPage = $request->get('per_page', 20);

        $query = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        $notifications = $query->paginate($perPage);

        // Count unread notifications
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ]
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => [
                'notification' => $notification->fresh()
            ]
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification(Request $request, $notificationId): JsonResponse
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Get notification preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $preferences = $user->notification_preferences ?? [
            'bet_results' => true,
            'promotions' => true,
            'system_updates' => true,
            'deposit_confirmation' => true,
            'withdrawal_updates' => true,
            'email_notifications' => true,
            'push_notifications' => true,
            'sms_notifications' => false,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'preferences' => $preferences
            ]
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bet_results' => 'nullable|boolean',
            'promotions' => 'nullable|boolean',
            'system_updates' => 'nullable|boolean',
            'deposit_confirmation' => 'nullable|boolean',
            'withdrawal_updates' => 'nullable|boolean',
            'email_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $currentPreferences = $user->notification_preferences ?? [];

        // Merge with existing preferences
        $newPreferences = array_merge($currentPreferences, $request->only([
            'bet_results',
            'promotions',
            'system_updates',
            'deposit_confirmation',
            'withdrawal_updates',
            'email_notifications',
            'push_notifications',
            'sms_notifications',
        ]));

        $user->update([
            'notification_preferences' => $newPreferences
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
            'data' => [
                'preferences' => $newPreferences
            ]
        ]);
    }

    /**
     * Send bulk notification (Admin only)
     */
    public function sendBulkNotification(Request $request): JsonResponse
    {
        // Check if user has admin privileges
        if (!$request->user()->hasRole(['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|string|in:system,promotion,announcement',
            'target_users' => 'nullable|array',
            'target_users.*' => 'integer|exists:users,id',
            'user_criteria' => 'nullable|array',
            'user_criteria.is_verified' => 'nullable|boolean',
            'user_criteria.has_deposited' => 'nullable|boolean',
            'user_criteria.registration_date_from' => 'nullable|date',
            'user_criteria.registration_date_to' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $title = $request->title;
            $message = $request->message;
            $type = $request->type;

            // Determine target users
            if ($request->has('target_users')) {
                $targetUsers = User::whereIn('id', $request->target_users)->get();
            } else {
                // Build query based on criteria
                $query = User::query();

                if ($request->has('user_criteria')) {
                    $criteria = $request->user_criteria;

                    if (isset($criteria['is_verified'])) {
                        $query->where('is_verified', $criteria['is_verified']);
                    }

                    if (isset($criteria['has_deposited'])) {
                        if ($criteria['has_deposited']) {
                            $query->whereHas('transactions', function($q) {
                                $q->where('type', 'deposit')
                                  ->where('status', 'completed');
                            });
                        } else {
                            $query->whereDoesntHave('transactions', function($q) {
                                $q->where('type', 'deposit')
                                  ->where('status', 'completed');
                            });
                        }
                    }

                    if (isset($criteria['registration_date_from'])) {
                        $query->whereDate('created_at', '>=', $criteria['registration_date_from']);
                    }

                    if (isset($criteria['registration_date_to'])) {
                        $query->whereDate('created_at', '<=', $criteria['registration_date_to']);
                    }
                }

                $targetUsers = $query->get();
            }

            // Create notifications for each user
            $notificationsData = [];
            foreach ($targetUsers as $user) {
                $notificationsData[] = [
                    'user_id' => $user->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert notifications
            if (!empty($notificationsData)) {
                Notification::insert($notificationsData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk notification sent successfully',
                'data' => [
                    'sent_to' => count($notificationsData),
                    'title' => $title,
                    'type' => $type
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send bulk notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $unreadCount
            ]
        ]);
    }
}
