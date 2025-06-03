<div>
    <div class="min-h-screen flex bg-gradient-to-br from-slate-50 via-stone-50 to-amber-50">
        <div class="fixed inset-0 opacity-3 pointer-events-none">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(154, 130, 17, 0.1) 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <!-- Loading overlay for non-polling actions only -->
        <div wire:loading.flex wire:target="sendMessage,selectUser,deleteMessage,refreshMessages" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
            <div class="bg-white rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-amber-600"></div>
                    <span class="text-gray-700 font-medium">Loading...</span>
                </div>
            </div>
        </div>

        <div class="w-1/3 bg-white shadow-lg border-r border-gray-100 p-6 h-screen overflow-y-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 mb-4">
                    <i class="fas fa-envelope text-2xl text-amber-700"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center justify-center">
                    Chats
                    @if($unreadCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">{{ $unreadCount }}</span>
                    @endif
                </h2>
                <!-- Enhanced Polling Status Indicator -->
                <div class="mt-2 flex items-center justify-center space-x-2">
                    <div id="polling-status" class="w-2 h-2 bg-green-500 rounded-full transition-all duration-300"></div>
                    <span id="polling-text" class="text-xs text-gray-500 transition-all duration-300">Live updates</span>
                </div>
                <div id="last-update" class="text-xs text-gray-400 mt-1 opacity-0 transition-opacity duration-300"></div>
            </div>

            <button wire:click="startNewMessage" 
                    class="w-full bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold py-2 px-4 rounded-xl mb-6 transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                <i class="fas fa-plus mr-2"></i>
                New Message
            </button>

            <div class="mb-6 relative">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="w-full px-4 py-2 pr-10 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500" 
                       placeholder="Search contacts..."
                       maxlength="100">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                    @if($search)
                        <button wire:click="clearSearch" 
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                                type="button">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    @endif
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <div wire:loading.delay wire:target="search" class="absolute right-12 top-1/2 transform -translate-y-1/2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-amber-600"></div>
                </div>
            </div>

            <!-- Manual Refresh Button -->
            <div class="mb-4 text-center">
                <button wire:click="refreshMessages" 
                        class="text-amber-600 hover:text-amber-700 text-sm font-medium transition-colors"
                        title="Refresh messages"
                        wire:loading.class="opacity-50">
                    <i class="fas fa-sync-alt mr-1" wire:loading.class="animate-spin" wire:target="refreshMessages"></i>
                    Refresh
                </button>
            </div>

            <div class="space-y-4">
                @if($users && $users->count() > 0)
                    @foreach ($users as $user)
                        @if(isset($user->other_user))
                            <div class="flex items-center p-3 bg-gray-50 rounded-2xl hover:bg-amber-50 cursor-pointer transition-all duration-300 {{ $selectedUserId == $user->other_user->id ? 'bg-amber-100 border-2 border-amber-300' : '' }}" 
                                 wire:click="selectUser({{ $user->other_user->id }})"
                                 wire:loading.class="opacity-50 cursor-not-allowed"
                                 wire:target="selectUser">
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border-2 border-amber-200 flex items-center justify-center">
                                        @if($user->other_user->profile_photo_path)
                                            <img src="{{ $user->other_user->profile_photo_url }}" alt="Profile" class="w-full h-full rounded-2xl object-cover">
                                        @else
                                            <span class="text-lg font-bold text-amber-800">
                                                {{ strtoupper(substr($user->other_user->first_name ?? $user->other_user->username ?? 'U', 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $user->other_user->first_name ?? 'Unknown' }} {{ $user->other_user->last_name ?? '' }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $user->other_user->username ?? 'N/A' }}</p>
                                    @if(isset($user->last_message_preview))
                                        <p class="text-xs text-gray-500 truncate max-w-32">{{ $user->last_message_preview }}</p>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500 flex flex-col items-end">
                                    <i class="fas fa-comment-dots mr-1"></i>
                                    @if(isset($user->unread_count) && $user->unread_count > 0)
                                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full mt-1 animate-pulse">
                                            {{ $user->unread_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if(method_exists($users, 'links'))
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-search text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-600">
                            @if($search)
                                No contacts found matching "{{ $search }}"
                            @else
                                No contacts found
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main content area - NO wire:poll here -->
        <div class="w-2/3 p-6 h-screen overflow-y-auto">
            @if($newMessageMode)
                <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100 h-full">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-user-plus mr-2 text-amber-600"></i>
                            New Message
                        </h2>
                        <button wire:click="$set('newMessageMode', false)" 
                                class="text-gray-500 hover:text-gray-700 transition-colors"
                                type="button">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="mb-6 relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="newMessageSearch" 
                               class="w-full px-4 py-2 pr-10 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500" 
                               placeholder="Search by name or username..."
                               maxlength="100">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                            @if($newMessageSearch)
                                <button wire:click="$set('newMessageSearch', '')" 
                                        class="text-gray-400 hover:text-gray-600 transition-colors"
                                        type="button">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            @endif
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <div wire:loading.delay wire:target="newMessageSearch" class="absolute right-12 top-1/2 transform -translate-y-1/2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-amber-600"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @if($newMessageUsers && $newMessageUsers->count() > 0)
                            @foreach ($newMessageUsers as $user)
                                <div class="flex items-center p-3 bg-gray-50 rounded-2xl hover:bg-amber-50 cursor-pointer transition-all duration-300" 
                                     wire:click="selectUser({{ $user->id }})"
                                     wire:loading.class="opacity-50 cursor-not-allowed"
                                     wire:target="selectUser">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border-2 border-amber-200 flex items-center justify-center">
                                        @if($user->profile_photo_path)
                                            <img src="{{ $user->profile_photo_url }}" alt="Profile" class="w-full h-full rounded-2xl object-cover">
                                        @else
                                            <span class="text-lg font-bold text-amber-800">
                                                {{ strtoupper(substr($user->first_name ?? $user->username ?? 'U', 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $user->username ?? 'N/A' }}</p>
                                        @if($user->email)
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-user-search text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-600">
                                    @if($newMessageSearch)
                                        No users found matching "{{ $newMessageSearch }}"
                                    @else
                                        Start typing to search for users
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($selectedUser)
                <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100 h-full flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border-2 border-amber-200 flex items-center justify-center">
                                @if($selectedUser->profile_photo_path)
                                    <img src="{{ $selectedUser->profile_photo_url }}" alt="Profile" class="w-full h-full rounded-2xl object-cover">
                                @else
                                    <span class="text-lg font-bold text-amber-800">
                                        {{ strtoupper(substr($selectedUser->first_name ?? $selectedUser->username ?? 'U', 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $selectedUser->first_name ?? 'Unknown' }} {{ $selectedUser->last_name ?? '' }}
                                </p>
                                <p class="text-sm text-gray-600">{{ $selectedUser->username ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Connection status indicator -->
                            <div id="connection-status" class="flex items-center space-x-1">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-xs text-gray-500">Online</span>
                            </div>
                            <button wire:click="$set('selectedUser', null)" 
                                    class="text-gray-500 hover:text-gray-700 transition-colors lg:hidden"
                                    type="button">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto mb-6 space-y-4 p-4 bg-gray-50 rounded-2xl border border-gray-200" id="messages-container">
                        @if($conversationMessages && is_countable($conversationMessages) && count($conversationMessages) > 0)
                            @foreach ($conversationMessages as $message)
                                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }} message-item" 
                                     data-message-id="{{ $message->id }}">
                                    <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl {{ $message->sender_id === Auth::id() ? 'bg-gradient-to-br from-amber-500 to-yellow-500 text-white' : 'bg-white border border-gray-200 text-gray-900' }} shadow-sm relative group">
                                        @if($message->sender_id === Auth::id())
                                            <!-- Sent message - show delete option -->
                                            <div class="absolute -left-8 top-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <button wire:click="deleteMessage({{ $message->id }})" 
                                                        class="text-red-500 hover:text-red-700 text-sm p-1 rounded-full hover:bg-red-50 transition-colors"
                                                        title="Delete message"
                                                        onclick="return confirm('Are you sure you want to delete this message?')">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        @endif
                                        
                                        <div class="text-sm">
                                            {!! $this->getFormattedContent($message->content) !!}
                                        </div>
                                        
                                        <div class="text-xs {{ $message->sender_id === Auth::id() ? 'text-yellow-100' : 'text-gray-500' }} mt-2 flex items-center justify-between">
                                            <span>{{ $message->created_at->format('g:i A') }}</span>
                                            @if($message->sender_id === Auth::id())
                                                <span class="flex items-center ml-2">
                                                    @if($message->read_at)
                                                        <i class="fas fa-check-double text-xs" title="Read"></i>
                                                    @else
                                                        <i class="fas fa-check text-xs" title="Sent"></i>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-comment text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-600 text-lg">No messages yet</p>
                                <p class="text-gray-500 text-sm mt-2">Start the conversation by sending a message!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Message input form -->
                    <form wire:submit.prevent="sendMessage" class="mt-auto">
                        <div class="flex space-x-3">
                            <div class="flex-1 relative">
                                <textarea wire:model.defer="messageContent" 
                                         class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500 resize-none" 
                                         placeholder="Type your message..." 
                                         rows="2"
                                         maxlength="1000"
                                         @keydown.enter.prevent="$wire.sendMessage()"
                                         @keydown.shift.enter.stop="$event.target.value += '\n'"
                                         wire:loading.attr="disabled"
                                         wire:target="sendMessage"></textarea>
                                
                                <!-- Character counter -->
                                <div class="absolute bottom-2 right-12 text-xs text-gray-400">
                                    <span x-data="{ count: $wire.entangle('messageContent').length || 0 }" 
                                          x-text="count + '/1000'"
                                          :class="count > 900 ? 'text-red-500' : count > 800 ? 'text-yellow-500' : 'text-gray-400'"></span>
                                </div>
                                
                                @error('messageContent')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold px-6 py-3 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled"
                                    wire:target="sendMessage">
                                <div wire:loading.remove wire:target="sendMessage">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div wire:loading wire:target="sendMessage">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <!-- Welcome screen when no user/conversation is selected -->
                <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100 h-full flex flex-col items-center justify-center">
                    <div class="text-center">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-3xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 flex items-center justify-center">
                            <i class="fas fa-comments text-4xl text-amber-600"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Welcome to Messages</h2>
                        <p class="text-gray-600 text-lg mb-8 max-w-md">
                            Select a conversation from the sidebar to start chatting, or create a new message to begin a conversation with someone new.
                        </p>
                        <button wire:click="startNewMessage" 
                                class="bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center mx-auto">
                            <i class="fas fa-plus mr-2"></i>
                            Start New Conversation
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for real-time polling and UI enhancements -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let pollingInterval;
            let isPolling = false;
            const POLLING_INTERVAL = {{ $pollingInterval }};

            // Start polling
            function startPolling() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
                
                pollingInterval = setInterval(() => {
                    if (!isPolling) {
                        isPolling = true;
                        updatePollingStatus('polling');
                        
                        @this.backgroundPoll().then(result => {
                            isPolling = false;
                            if (result && result.hasUpdates) {
                                updatePollingStatus('updated');
                            } else {
                                updatePollingStatus('connected');
                            }
                            updateLastUpdateTime();
                        }).catch(error => {
                            isPolling = false;
                            updatePollingStatus('error');
                            console.error('Polling error:', error);
                        });
                    }
                }, POLLING_INTERVAL);
            }

            // Update polling status indicator
            function updatePollingStatus(status) {
                const statusElement = document.getElementById('polling-status');
                const textElement = document.getElementById('polling-text');
                
                if (!statusElement || !textElement) return;

                switch(status) {
                    case 'connected':
                        statusElement.className = 'w-2 h-2 bg-green-500 rounded-full transition-all duration-300';
                        textElement.textContent = 'Live updates';
                        break;
                    case 'polling':
                        statusElement.className = 'w-2 h-2 bg-blue-500 rounded-full transition-all duration-300 animate-pulse';
                        textElement.textContent = 'Checking...';
                        break;
                    case 'updated':
                        statusElement.className = 'w-2 h-2 bg-amber-500 rounded-full transition-all duration-300 animate-bounce';
                        textElement.textContent = 'Updated';
                        setTimeout(() => updatePollingStatus('connected'), 2000);
                        break;
                    case 'error':
                        statusElement.className = 'w-2 h-2 bg-red-500 rounded-full transition-all duration-300';
                        textElement.textContent = 'Connection issue';
                        break;
                }
            }

            // Update last update timestamp
            function updateLastUpdateTime() {
                const lastUpdateElement = document.getElementById('last-update');
                if (lastUpdateElement) {
                    const now = new Date();
                    lastUpdateElement.textContent = `Last update: ${now.toLocaleTimeString()}`;
                    lastUpdateElement.classList.remove('opacity-0');
                    lastUpdateElement.classList.add('opacity-100');
                    
                    setTimeout(() => {
                        lastUpdateElement.classList.remove('opacity-100');
                        lastUpdateElement.classList.add('opacity-0');
                    }, 3000);
                }
            }

            // Scroll to bottom of messages
            function scrollToBottom() {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }

            // Auto-scroll on new messages
            window.addEventListener('scrollToBottom', scrollToBottom);
            
            // Listen for Livewire events
            window.addEventListener('newMessageReceived', () => {
                scrollToBottom();
                updatePollingStatus('updated');
            });

            window.addEventListener('messagesUpdated', () => {
                updatePollingStatus('updated');
            });

            window.addEventListener('messagesRefreshed', () => {
                updatePollingStatus('updated');
                scrollToBottom();
            });

            // Start polling when page loads
            startPolling();
            updatePollingStatus('connected');

            // Handle visibility change to pause/resume polling
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                    }
                    updatePollingStatus('paused');
                } else {
                    startPolling();
                    updatePollingStatus('connected');
                }
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
            });

            // Auto-focus message input when user is selected
            window.addEventListener('userSelected', () => {
                setTimeout(() => {
                    const messageInput = document.querySelector('textarea[wire\\:model\\:defer="messageContent"]');
                    if (messageInput) {
                        messageInput.focus();
                    }
                    scrollToBottom();
                }, 100);
            });

            // Initial scroll to bottom if messages exist
            setTimeout(scrollToBottom, 100);
        });
    </script>

    @push('styles')
        <style>
            /* Custom scrollbar for messages */
            #messages-container::-webkit-scrollbar {
                width: 6px;
            }
            
            #messages-container::-webkit-scrollbar-track {
                background: #f3f4f6;
                border-radius: 10px;
            }
            
            #messages-container::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 10px;
            }
            
            #messages-container::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            /* Message animation */
            .message-item {
                animation: messageSlideIn 0.3s ease-out;
            }

            @keyframes messageSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Responsive message bubbles */
            @media (max-width: 768px) {
                .max-w-xs {
                    max-width: 80%;
                }
            }
        </style>
    @endpush
</div>