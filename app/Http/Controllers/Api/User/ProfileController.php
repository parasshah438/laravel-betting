<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\KycDocument;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'profile',
            'kycDocuments' => function($query) {
                $query->latest();
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'phone' => 'nullable|string|max:20',
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'annual_income' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string|max:500',
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

            $user = $request->user();
            $oldUserData = $user->toArray();

            // Update user basic info
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            
            $user->save();

            // Update profile
            $profile = $user->profile;
            $oldProfileData = $profile ? $profile->toArray() : [];

            $profileData = $request->only([
                'first_name', 'last_name', 'address_line_1', 'address_line_2',
                'city', 'state', 'postal_code', 'occupation', 'annual_income', 'bio'
            ]);

            if ($profile) {
                $profile->update($profileData);
            } else {
                $profile = UserProfile::create(array_merge(
                    ['user_id' => $user->id],
                    $profileData
                ));
            }

            // Log profile update
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'profile_updated',
                'model_type' => User::class,
                'model_id' => $user->id,
                'old_values' => array_merge($oldUserData, $oldProfileData),
                'new_values' => array_merge($user->fresh()->toArray(), $profile->fresh()->toArray()),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user->fresh()->load('profile')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $profile = $user->profile;

            if (!$profile) {
                $profile = UserProfile::create(['user_id' => $user->id]);
            }

            // Delete old avatar if exists
            if ($profile->avatar && Storage::exists($profile->avatar)) {
                Storage::delete($profile->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            $profile->update(['avatar' => $avatarPath]);

            // Log avatar upload
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'avatar_uploaded',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => ['avatar_path' => $avatarPath]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar_url' => Storage::url($avatarPath)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avatar upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload KYC document
     */
    public function uploadKycDocument(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:passport,driving_license,national_id,utility_bill,bank_statement',
            'document_number' => 'nullable|string|max:255',
            'document_expiry' => 'nullable|date|after:today',
            'document' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Check if document type already exists and is approved
            $existingDoc = KycDocument::where('user_id', $user->id)
                ->where('document_type', $request->document_type)
                ->where('status', 'approved')
                ->first();

            if ($existingDoc) {
                return response()->json([
                    'success' => false,
                    'message' => 'This document type is already approved'
                ], 400);
            }

            // Store document
            $documentPath = $request->file('document')->store('kyc_documents', 'private');

            // Create KYC document record
            $kycDocument = KycDocument::create([
                'user_id' => $user->id,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'document_path' => $documentPath,
                'document_expiry' => $request->document_expiry,
                'status' => 'pending',
            ]);

            // Log KYC document upload
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'kyc_document_uploaded',
                'model_type' => KycDocument::class,
                'model_id' => $kycDocument->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'document_type' => $request->document_type,
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'KYC document uploaded successfully',
                'data' => [
                    'document' => $kycDocument
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'KYC document upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get KYC documents
     */
    public function getKycDocuments(Request $request): JsonResponse
    {
        $documents = KycDocument::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'documents' => $documents
            ]
        ]);
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'language' => 'nullable|string|size:2',
            'timezone' => 'nullable|string|max:50',
            'notifications' => 'nullable|array',
            'notifications.email' => 'boolean',
            'notifications.sms' => 'boolean',
            'notifications.push' => 'boolean',
            'betting_preferences' => 'nullable|array',
            'betting_preferences.default_stake' => 'nullable|numeric|min:0',
            'betting_preferences.odds_format' => 'nullable|in:decimal,fractional,american',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $oldPreferences = $user->preferences;

            $preferences = array_merge($user->preferences ?? [], $request->all());
            
            $user->preferences = $preferences;
            $user->save();

            // Log preferences update
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'preferences_updated',
                'old_values' => ['preferences' => $oldPreferences],
                'new_values' => ['preferences' => $preferences],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully',
                'data' => [
                    'preferences' => $preferences
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preferences update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
