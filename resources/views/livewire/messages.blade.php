<div>
    <div class="min-h-screen flex bg-gradient-to-br from-slate-50 via-stone-50 to-amber-50">
        <div class="fixed inset-0 opacity-3 pointer-events-none">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(154, 130, 17, 0.1) 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
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
                        <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </h2>
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

            <div class="space-y-4">
                @if($users && $users->count() > 0)
                    @foreach ($users as $user)
                        @if(isset($user->other_user))
                            <div class="flex items-center p-3 bg-gray-50 rounded-2xl hover:bg-amber-50 cursor-pointer transition-all duration-300 {{ $selectedUserId == $user->other_user->id ? 'bg-amber-100 border-2 border-amber-300' : '' }}" 
                                 wire:click="selectUser({{ $user->other_user->id }})"
                                 wire:loading.class="opacity-50 cursor-not-allowed">
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
                                    <a href="{{ route('profile.show', $user->other_user->username) }}" class="text-lg font-semibold text-gray-900 hover:underline">
                                        {{ $user->other_user->first_name ?? 'Unknown' }} {{ $user->other_user->last_name ?? '' }}
                                    </a>
                                    <p class="text-sm text-gray-600">{{ $user->other_user->username ?? 'N/A' }}</p>
                                    @if(isset($user->last_message_preview))
                                        <p class="text-xs text-gray-500 truncate max-w-32">{{ $user->last_message_preview }}</p>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500 flex flex-col items-end">
                                    <i class="fas fa-comment-dots mr-1"></i>
                                    @if(isset($user->unread_count) && $user->unread_count > 0)
                                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full mt-1">
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
                                     wire:loading.class="opacity-50 cursor-not-allowed">
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
                                        <a href="{{ route('profile.show', $user->username) }}" class="text-lg font-semibold text-gray-900 hover:underline">
                                            {{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}
                                        </a>
                                        <p class="text-sm text-gray-600">{{ $user->username ?? 'N/A' }}</p>
                                        @if($user->email)
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
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
                                <a href="{{ route('profile.show', $selectedUser->username) }}" class="text-2xl font-bold text-gray-900 hover:underline">
                                    {{ $selectedUser->first_name ?? 'Unknown' }} {{ $selectedUser->last_name ?? '' }}
                                </a>
                                <p class="text-sm text-gray-600">{{ $selectedUser->username ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <button wire:click="$set('selectedUser', null)" 
                                class="text-gray-500 hover:text-gray-700 transition-colors lg:hidden"
                                type="button">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto mb-6 space-y-4 p-4 bg-gray-50 rounded-2xl border border-gray-200" id="messages-container">
                        @if($conversationMessages && is_countable($conversationMessages) && count($conversationMessages) > 0)
                            @foreach ($conversationMessages as $message)
                                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md p-3 rounded-2xl {{ $message->sender_id === Auth::id() ? 'bg-amber-100 text-amber-900' : 'bg-white text-gray-800' }} shadow-md relative group">
                                        <p class="text-sm break-words">{!! nl2br(e($message->content)) !!}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-xs text-gray-500">
                                                {{ $message->created_at ? $message->created_at->diffForHumans() : 'Just now' }}
                                            </p>
                                            @if($message->sender_id === Auth::id())
                                                <div class="flex items-center space-x-1">
                                                    @if(isset($message->is_read) && $message->is_read)
                                                        <i class="fas fa-check-double text-blue-500 text-xs"></i>
                                                    @else
                                                        <i class="fas fa-check text-gray-400 text-xs"></i>
                                                    @endif
                                                    <button wire:click="deleteMessage({{ $message->id }})" 
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity text-red-500 hover:text-red-700 ml-2"
                                                            type="button"
                                                            wire:confirm="Are you sure you want to delete this message?">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-comments text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No messages yet</h3>
                                <p class="text-gray-500">Start the conversation with {{ $selectedUser->first_name ?? $selectedUser->username ?? 'this user' }}!</p>
                            </div>
                        @endif
                    </div>

                    <form wire:submit.prevent="sendMessage" class="flex-shrink-0">
                        <div class="flex items-end space-x-3">
                            <div class="flex-1">
                                <textarea 
                                    wire:model="messageContent"
                                    rows="3"
                                    maxlength="1000"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500 resize-none"
                                    placeholder="Type your message..."
                                    wire:keydown.ctrl.enter="sendMessage"
                                    wire:keydown.cmd.enter="sendMessage"
                                ></textarea>
                                @error('messageContent')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <div class="flex justify-between items-center mt-1 px-1">
                                    <span class="text-xs text-gray-400">
                                        {{ strlen($messageContent ?? '') }}/1000 characters
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        Ctrl + Enter to send
                                    </span>
                                </div>
                            </div>
                            <button 
                                type="submit"
                                class="bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold px-6 py-3 rounded-2xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                wire:loading.attr="disabled"
                                wire:target="sendMessage"
                            >
                                <div wire:loading.remove wire:target="sendMessage">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div wire:loading wire:target="sendMessage">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                </div>
                            </button>
                        </div>
                    </form>

                    <!-- Echo Listener -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            if (window.Echo) {
                                window.Echo.channel('messages.' + @json(Auth::id()))
                                    .listen('.MessageSent', (e) => {
                                        console.log('New message received:', e);
                                        @this.call('messageReceived', e);
                                    });
                            } else {
                                console.error('Echo is not initialized');
                            }
                        });
                    </script>
                </div>
            @else
                <div class="text-center h-full flex items-center justify-center bg-white shadow-lg rounded-3xl p-8 border border-gray-100">
                    <div>
                        <div class="w-32 h-32 mx-auto mb-6 rounded-3xl bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center border border-gray-200">
                            <i class="fas fa-comments text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Select a Chat</h3>
                        <p class="text-gray-600 text-lg max-w-md mx-auto">Click a contact to start messaging or create a new message.</p>
                        <button wire:click="startNewMessage" 
                                class="mt-4 bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold py-2 px-6 rounded-xl transition-all duration-300 transform hover:scale-105"
                                type="button">
                            <i class="fas fa-plus mr-2"></i>
                            Start New Chat
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center animate-slide-in">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center animate-slide-in">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <style>
        @keyframes slide-in { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fade-out { from { opacity: 1; } to { opacity: 0; } }
        .animate-slide-in { animation: slide-in 0.4s ease-out; }
        .animate-fade-out { animation: fade-out 0.3s ease-out forwards; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #d97706, #f59e0b); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(to bottom, #b45309, #d97706); }
        * { transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); }
        #messages-container { scroll-behavior: smooth; }
        @media (max-width: 768px) {
            .min-h-screen.flex { flex-direction: column; }
            .w-1\/3, .w-2\/3 { width: 100%; }
            .h-screen { height: auto; min-height: 50vh; }
        }
        input:focus, textarea:focus { box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
        button:hover { transform: translateY(-1px); }
        button:active { transform: translateY(0); }
        button:disabled { opacity: 0.7; transform: none; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function() {
                const flashMessages = document.querySelectorAll('.animate-slide-in');
                flashMessages.forEach(function(message) {
                    message.classList.add('animate-fade-out');
                    setTimeout(function() {
                        message.remove();
                    }, 300);
                });
            }, 5000);

            function scrollToBottom() {
                const messagesContainer = document.getElementById('messages-container');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }

            window.addEventListener('message-sent', function(event) {
                scrollToBottom();
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-xl shadow-lg z-50 animate-slide-in';
                notification.innerHTML = `<i class="fas fa-check mr-2"></i>Message sent to ${event.detail.recipient}!`;
                document.body.appendChild(notification);
                setTimeout(function() {
                    notification.classList.add('animate-fade-out');
                    setTimeout(function() {
                        notification.remove();
                    }, 300);
                }, 3000);
            });

            window.addEventListener('scrollToBottom', function() {
                scrollToBottom();
            });

            window.addEventListener('show-success', function(event) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center animate-slide-in';
                notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${event.detail.message}`;
                document.body.appendChild(notification);
                setTimeout(function() {
                    notification.classList.add('animate-fade-out');
                    setTimeout(function() {
                        notification.remove();
                    }, 300);
                }, 3000);
            });

            window.addEventListener('show-error', function(event) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center animate-slide-in';
                notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${event.detail.message}`;
                document.body.appendChild(notification);
                setTimeout(function() {
                    notification.classList.add('animate-fade-out');
                    setTimeout(function() {
                        notification.remove();
                    }, 300);
                }, 3000);
            });

            setTimeout(scrollToBottom, 100);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    @this.set('newMessageMode', false);
                }
            });

            let searchTimeout;
            const searchInputs = document.querySelectorAll('input[wire\\:model\\.live\\.debounce]');
            searchInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        input.style.borderColor = '#f59e0b';
                        setTimeout(function() {
                            input.style.borderColor = '';
                        }, 200);
                    }, 100);
                });
            });
        });

        document.addEventListener('livewire:initialized', function () {
            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    const messagesContainer = document.getElementById('messages-container');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                }, 50);
            });
        });
    </script>
</div>