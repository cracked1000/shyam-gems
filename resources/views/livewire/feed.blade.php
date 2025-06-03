<div wire:poll.10s="checkForUpdates">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-stone-50 to-amber-50 p-6">
        <!-- Subtle Background Pattern -->
        <div class="fixed inset-0 opacity-3 pointer-events-none">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(154, 130, 17, 0.1) 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center animate-slide-in">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('message') }}
            </div>
        @endif


        <!-- Main Content -->
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Post Requirement Form -->
            <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-plus-circle mr-2 text-amber-600"></i>
                    Post a Requirement
                </h2>
                <form wire:submit.prevent="postRequirement" class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" wire:model="title" id="title"
                               class="mt-1 w-full px-4 py-2 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500"
                               placeholder="Enter requirement title">
                        @error('title')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" id="description" rows="3"
                                  class="mt-1 w-full px-4 py-2 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500 resize-none"
                                  placeholder="Describe your requirement"></textarea>
                        @error('description')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="requirementImage" class="block text-sm font-medium text-gray-700">Image (Optional)</label>
                        <input type="file" wire:model="requirementImage" id="requirementImage"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                        @error('requirementImage')
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Post Requirement
                    </button>
                </form>
            </div>

            <!-- Requirements Section -->
            <div class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-list mr-2 text-amber-600"></i>
                    Requirements
                    @if($requirements->count() > 0)
                        <span class="ml-2 text-sm bg-amber-100 text-amber-800 px-3 py-1 rounded-full">
                            {{ $requirements->count() }}
                        </span>
                    @endif
                </h2>
                @if($requirements->isEmpty())
                    <div class="text-center py-6 text-gray-600">
                        No requirements posted yet.
                    </div>
                @else
                    @foreach($requirements as $requirement)
                        <div class="bg-white shadow-lg rounded-3xl p-6 border border-gray-100">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border-2 border-amber-200 flex items-center justify-center">
                                    @if($requirement->user && $requirement->user->profile_photo_path)
                                        <img src="{{ $requirement->user->profile_photo_url }}" alt="Profile" class="w-full h-full rounded-2xl object-cover">
                                    @else
                                        <span class="text-lg font-bold text-amber-800">
                                            {{ strtoupper(substr($requirement->user->first_name ?? 'U', 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <a href="{{ route('profile.show', $requirement->user->username) }}" class="text-lg font-semibold text-gray-900 hover:underline">
                                        {{ $requirement->user->first_name ?? 'Unknown' }} {{ $requirement->user->last_name ?? '' }}
                                    </a>
                                    <p class="text-sm text-gray-600">{{ $requirement->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $requirement->title }}</h4>
                            <p class="text-gray-700 mb-4">{{ $requirement->description }}</p>
                            @if($requirement->image)
                                <img src="{{ Storage::url($requirement->image) }}" alt="Requirement Image" class="w-full h-64 object-cover rounded-2xl mb-4">
                            @endif

                            <!-- Reply Section -->
                            @if(Auth::user()->role === 'seller')
                                <button wire:click="toggleProposalForm({{ $requirement->id }})"
                                        class="bg-amber-600 text-white px-4 py-2 rounded-xl hover:bg-amber-700 transition-all duration-300">
                                    {{ $showProposalForm && $selectedRequirementId == $requirement->id ? 'Cancel' : 'Submit Proposal' }}
                                </button>
                            @endif

                            @if($showProposalForm && $selectedRequirementId == $requirement->id)
                                <form wire:submit.prevent="postReply" class="mt-4 space-y-4">
                                    <div>
                                        <label for="replyContent" class="block text-sm font-medium text-gray-700">Your Proposal</label>
                                        <textarea wire:model="replyContent" id="replyContent" rows="3"
                                                  class="mt-1 w-full px-4 py-2 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500 resize-none"
                                                  placeholder="Describe your proposal"></textarea>
                                        @error('replyContent')
                                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="replyImage" class="block text-sm font-medium text-gray-700">Image (Optional)</label>
                                        <input type="file" wire:model="replyImage" id="replyImage"
                                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                        @error('replyImage')
                                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <button type="submit"
                                            class="w-full bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Submit Proposal
                                    </button>
                                </form>
                            @endif

                            <!-- Replies -->
                            @if($requirement->replies->count() > 0)
                                <div class="mt-6">
                                    <h5 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                        Proposals
                                        <span class="ml-2 text-sm bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            {{ $requirement->replies->count() }}
                                        </span>
                                    </h5>
                                    @foreach($requirement->replies as $reply)
                                        <div class="bg-gray-50 rounded-2xl p-4 mb-3">
                                            <div class="flex items-center mb-2">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-100 to-yellow-50 border-2 border-amber-200 flex items-center justify-center">
                                                    @if($reply->user && $reply->user->profile_photo_path)
                                                        <img src="{{ $reply->user->profile_photo_url }}" alt="Profile" class="w-full h-full rounded-full object-cover">
                                                    @else
                                                        <span class="text-sm font-bold text-amber-800">
                                                            {{ strtoupper(substr($reply->user->first_name ?? 'U', 0, 1)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="ml-2">
                                                    <a href="{{ route('profile.show', $reply->user->username) }}" class="text-sm font-medium text-gray-900 hover:underline">
                                                        {{ $reply->user->first_name ?? 'Unknown' }} {{ $reply->user->last_name ?? '' }}
                                                    </a>
                                                    <p class="text-xs text-gray-600">{{ $reply->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <p class="text-gray-700">{{ $reply->content }}</p>
                                            @if($reply->image)
                                                <img src="{{ Storage::url($reply->image) }}" alt="Reply Image" class="w-full h-48 object-cover rounded-2xl mt-3">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Styles -->
    <style>
        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fade-out {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.4s ease-out;
        }

        .animate-fade-out {
            animation: fade-out 0.3s ease-out forwards;
        }

        /* Premium scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #d97706, #f59e0b);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #b45309, #d97706);
        }

        /* Smooth transitions */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    <!-- JavaScript for Alerts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-hide flash messages
            setTimeout(function() {
                const flashMessages = document.querySelectorAll('.animate-slide-in');
                flashMessages.forEach(function(message) {
                    message.classList.add('animate-fade-out');
                    setTimeout(function() {
                        message.remove();
                    }, 300);
                });
            }, 5000);
        });

        document.addEventListener('livewire:init', function () {
            Livewire.on('alert', data => {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 bg-${data[0].type === 'success' ? 'green' : data[0].type === 'error' ? 'red' : 'blue'}-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center animate-slide-in`;
                notification.innerHTML = `<i class="fas fa-${data[0].type === 'success' ? 'check' : data[0].type === 'error' ? 'exclamation' : 'info'}-circle mr-2"></i>${data[0].message}`;
                document.body.appendChild(notification);

                setTimeout(function() {
                    notification.classList.add('animate-fade-out');
                    setTimeout(function() {
                        notification.remove();
                    }, 300);
                }, 3000);
            });
        });
    </script>
</div>