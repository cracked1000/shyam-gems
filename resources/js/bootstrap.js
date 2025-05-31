import axios from 'axios';
window.axios = axios;

// Set up CSRF token for all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add auth token for Sanctum if available
const authToken = localStorage.getItem('auth_token') || document.querySelector('meta[name="auth-token"]')?.content;
if (authToken) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
}

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Enhanced Echo configuration with authentication
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    enabledTransports: ['ws', 'wss'],
    disabledTransports: ['sockjs', 'pusher'],
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    
    // Enhanced authentication for private/presence channels
    auth: {
        headers: {
            'Authorization': authToken ? `Bearer ${authToken}` : '',
            'X-CSRF-TOKEN': token ? token.content : '',
        },
    },
    
    // Connection event handlers
    connecting: () => {
        console.log('Echo: Connecting to WebSocket...');
    },
    
    connected: () => {
        console.log('Echo: Connected to WebSocket');
        // Update user online status
        if (window.userId) {
            window.axios.post('/api/user/online-status', { is_online: true })
                .catch(error => console.warn('Failed to update online status:', error));
        }
    },
    
    disconnected: () => {
        console.log('Echo: Disconnected from WebSocket');
        // Update user offline status
        if (window.userId) {
            window.axios.post('/api/user/online-status', { is_online: false })
                .catch(error => console.warn('Failed to update offline status:', error));
        }
    },
    
    error: (error) => {
        console.error('Echo Error:', error);
    }
});

// Global user data (set this in your layout)
window.currentUser = window.currentUser || {};
window.userId = window.currentUser.id;
window.userRole = window.currentUser.role;

// Helper functions for real-time features
window.EchoHelpers = {
    // Join user's private channel
    joinUserChannel: function(userId = null) {
        const id = userId || window.userId;
        if (!id) return null;
        
        return window.Echo.private(`user.${id}`);
    },
    
    // Join role-based channels
    joinRoleChannel: function(role = null) {
        const userRole = role || window.userRole;
        if (!userRole) return null;
        
        return window.Echo.private(`role.${userRole}`);
    },
    
    // Join conversation channel for messaging
    joinConversationChannel: function(conversationId) {
        return window.Echo.private(`conversation.${conversationId}`);
    },
    
    // Join presence channel (for seeing who's online)
    joinPresenceChannel: function(channelName) {
        return window.Echo.join(channelName);
    },
    
    // Leave a channel
    leaveChannel: function(channelName) {
        window.Echo.leave(channelName);
    }
};

// Auto-join user's private channel on connection
window.Echo.connector.socket.on('connect', () => {
    if (window.userId) {
        window.EchoHelpers.joinUserChannel()
            .notification((notification) => {
                console.log('User notification received:', notification);
                // Handle notifications (show toast, update UI, etc.)
                if (window.handleNotification) {
                    window.handleNotification(notification);
                }
            });
    }
});

// Handle page visibility for online status
document.addEventListener('visibilitychange', () => {
    if (window.userId) {
        const isOnline = !document.hidden;
        window.axios.post('/api/user/online-status', { is_online: isOnline })
            .catch(error => console.warn('Failed to update visibility status:', error));
    }
});

// Handle beforeunload to set offline status
window.addEventListener('beforeunload', () => {
    if (window.userId) {
        // Use sendBeacon for reliable offline status update
        const data = new FormData();
        data.append('is_online', 'false');
        data.append('_token', token ? token.content : '');
        
        navigator.sendBeacon('/api/user/online-status', data);
    }
});

import './echo';