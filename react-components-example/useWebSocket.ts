// Real-time WebSocket Hook for Live Betting
import { useEffect, useRef, useState } from 'react';
import { useDispatch } from 'react-redux';
import { updateLiveOdds, updateMatchScore } from '../store/slices/liveDataSlice';

interface UseWebSocketOptions {
  url: string;
  onConnect?: () => void;
  onDisconnect?: () => void;
  onError?: (error: Event) => void;
}

export const useWebSocket = ({ url, onConnect, onDisconnect, onError }: UseWebSocketOptions) => {
  const ws = useRef<WebSocket | null>(null);
  const dispatch = useDispatch();
  const [isConnected, setIsConnected] = useState(false);
  const reconnectTimeoutRef = useRef<NodeJS.Timeout>();
  const reconnectAttemptsRef = useRef(0);
  const maxReconnectAttempts = 5;

  const connect = () => {
    try {
      ws.current = new WebSocket(url);
      
      ws.current.onopen = () => {
        console.log('WebSocket connected');
        setIsConnected(true);
        reconnectAttemptsRef.current = 0;
        onConnect?.();
      };

      ws.current.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data);
          
          switch (data.type) {
            case 'odds_update':
              dispatch(updateLiveOdds({
                matchId: data.matchId,
                odds: data.odds,
                timestamp: data.timestamp
              }));
              break;
              
            case 'score_update':
              dispatch(updateMatchScore({
                matchId: data.matchId,
                score: data.score,
                time: data.time
              }));
              break;
              
            case 'match_status':
              // Handle match start/end/pause
              break;
              
            default:
              console.log('Unknown message type:', data.type);
          }
        } catch (error) {
          console.error('Error parsing WebSocket message:', error);
        }
      };

      ws.current.onclose = () => {
        console.log('WebSocket disconnected');
        setIsConnected(false);
        onDisconnect?.();
        
        // Auto-reconnect with exponential backoff
        if (reconnectAttemptsRef.current < maxReconnectAttempts) {
          const delay = Math.pow(2, reconnectAttemptsRef.current) * 1000;
          console.log(`Reconnecting in ${delay}ms... (attempt ${reconnectAttemptsRef.current + 1}/${maxReconnectAttempts})`);
          
          reconnectTimeoutRef.current = setTimeout(() => {
            reconnectAttemptsRef.current++;
            connect();
          }, delay);
        }
      };

      ws.current.onerror = (error) => {
        console.error('WebSocket error:', error);
        onError?.(error);
      };
    } catch (error) {
      console.error('Error creating WebSocket connection:', error);
    }
  };

  const disconnect = () => {
    if (reconnectTimeoutRef.current) {
      clearTimeout(reconnectTimeoutRef.current);
    }
    if (ws.current) {
      ws.current.close();
    }
  };

  const sendMessage = (message: any) => {
    if (ws.current && ws.current.readyState === WebSocket.OPEN) {
      ws.current.send(JSON.stringify(message));
    } else {
      console.warn('WebSocket is not connected');
    }
  };

  useEffect(() => {
    connect();
    
    return () => {
      disconnect();
    };
  }, [url]);

  return {
    isConnected,
    sendMessage,
    disconnect,
    reconnect: connect
  };
};

// Custom hook for live betting data
export const useLiveBetting = () => {
  const { isConnected, sendMessage } = useWebSocket({
    url: process.env.REACT_APP_WEBSOCKET_URL || 'ws://localhost:8080',
    onConnect: () => {
      // Subscribe to live betting updates
      sendMessage({
        type: 'subscribe',
        channels: ['odds_updates', 'score_updates', 'match_status']
      });
    }
  });

  const subscribeTo

Match = (matchId: string) => {
    sendMessage({
      type: 'subscribe_match',
      matchId
    });
  };

  const unsubscribeFromMatch = (matchId: string) => {
    sendMessage({
      type: 'unsubscribe_match',
      matchId
    });
  };

  return {
    isConnected,
    subscribeToMatch,
    unsubscribeFromMatch
  };
};
