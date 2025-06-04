@php
    use Illuminate\Support\Facades\Auth;
@endphp

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-[#9a8211] to-[#b8951a] px-8 py-12">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">Welcome Back!</h1>
                        <p class="text-[#f5f1e8] text-lg">Manage your precious gem collection</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            @if(Auth::check() && $user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile" class="w-20 h-20 rounded-full object-cover border-4 border-white/30">
                            @else
                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-8">
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            @if(Auth::check() && $user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile" class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-[#9a8211]/20">
                            @else
                                <div class="w-24 h-24 bg-gradient-to-br from-[#9a8211]/20 to-[#9a8211]/10 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-12 h-12 text-[#9a8211]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-400 rounded-full border-2 border-white"></div>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mt-4">
                            {{ $user->first_name ?? 'Gem' }} {{ $user->last_name ?? 'Seller' }}
                        </h2>
                        <p class="text-lg text-gray-600">{{ '@' . $user->username }}</p>
                    </div>

                    @if(Auth::check())
                        <div class="space-y-4">
                            <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                <svg class="w-5 h-5 text-[#9a8211] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-700">{{ $user->email ?? 'email@example.com' }}</span>
                            </div>

                            <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                <svg class="w-5 h-5 text-[#9a8211] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-700">{{ $user->telephone ?? 'Not provided' }}</span>
                            </div>

                            @if($user->bio)
                                <div class="p-4 bg-gradient-to-r from-[#9a8211]/5 to-[#9a8211]/10 rounded-xl">
                                    <p class="text-sm text-gray-700 italic">"{{ $user->bio }}"</p>
                                </div>
                            @endif

                            @if($user->experience)
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <svg class="w-5 h-5 text-[#9a8211] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Experience</p>
                                        <p class="text-gray-700 font-medium">{{ $user->experience }} years</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center p-6 bg-red-50 rounded-xl">
                            <p class="text-red-600">User not authenticated</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Gem Collection Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-[#9a8211]/10 to-[#9a8211]/5 px-8 py-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Your Gem Collection</h3>
                                <p class="text-gray-600">Showcase your precious stones to the world</p>
                            </div>
                            <div class="bg-[#9a8211]/10 px-3 py-1 rounded-full">
                                <span class="text-[#9a8211] font-semibold text-sm">{{ count($gems) }} {{ Str::plural('Gem', count($gems)) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <!-- Upload Form -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-2xl p-6 mb-8 border border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-[#9a8211] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add New Gem
                            </h4>
                            
                            <form wire:submit.prevent="storeGem" enctype="multipart/form-data" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Gem Name</label>
                                        <input id="name" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-[#9a8211] focus:border-[#9a8211] transition-all duration-200" type="text" wire:model="name" placeholder="e.g., Blue Sapphire" required />
                                        @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Gem Image</label>
                                        <input id="image" type="file" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-[#9a8211] focus:border-[#9a8211] transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#9a8211]/10 file:text-[#9a8211] hover:file:bg-[#9a8211]/20" wire:model="image" accept="image/*" required />
                                        @error('image') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-[#9a8211] focus:border-[#9a8211] transition-all duration-200 resize-none" wire:model="description" placeholder="Describe your gem's unique features..." required></textarea>
                                    @error('description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-[#9a8211] to-[#b8951a] border border-transparent rounded-xl font-semibold text-white hover:from-[#8a7310] hover:to-[#a8851a] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6 0h6m-6 0H6"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="storeGem">Add to Collection</span>
                                    <span wire:loading wire:target="storeGem">Adding...</span>
                                </button>
                            </form>
                        </div>

                        <!-- Gem Gallery -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @forelse ($gems as $gem)
                                <div class="group relative bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                    <!-- Image Container with Click Event -->
                                    <div class="relative aspect-square overflow-hidden cursor-pointer" wire:click="showImage('{{ asset('storage/' . $gem->image) }}')">
                                        @if($gem->image && Storage::disk('public')->exists($gem->image))
                                            <img src="{{ asset('storage/' . $gem->image) }}" alt="{{ $gem->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Gradient Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        
                                        <!-- Content Overlay -->
                                        <div class="absolute inset-0 flex flex-col justify-end p-6 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                            <h4 class="text-xl font-bold mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">{{ $gem->name }}</h4>
                                            <p class="text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-200 line-clamp-3">{{ $gem->description }}</p>
                                        </div>
                                        
                                        <!-- Delete Button with Confirmation -->
                                        <button 
                                            wire:click.stop="confirmDelete('{{ $gem->_id }}')" 
                                            class="absolute top-4 right-4 w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-110"
                                            title="Delete {{ $gem->name }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Card Footer -->
                                    <div class="p-4 bg-gradient-to-r from-[#9a8211]/5 to-transparent">
                                        <h4 class="font-semibold text-gray-900 truncate">{{ $gem->name }}</h4>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $gem->description }}</p>
                                        <div class="flex items-center justify-between mt-3">
                                            <span class="text-xs text-gray-500">{{ $gem->created_at->diffForHumans() }}</span>
                                            <button 
                                                wire:click="confirmDelete('{{ $gem->_id }}')"
                                                class="text-red-500 hover:text-red-700 transition-colors duration-200"
                                                title="Delete gem"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-[#9a8211]/20 to-[#9a8211]/10 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-[#9a8211]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No gems in your collection yet</h3>
                                    <p class="text-gray-600 mb-6 max-w-md">Start building your precious gem collection by adding your first stone above.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Image Preview Modal -->
                @if ($selectedImage)
                    <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" wire:click.self="closeImage">
                        <div class="bg-white rounded-2xl p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto mx-4">
                            <div class="flex justify-end mb-4">
                                <button wire:click="closeImage" class="text-gray-500 hover:text-gray-700 text-3xl font-bold w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">×</button>
                            </div>
                            <img src="{{ $selectedImage }}" alt="Full Screen Gem" class="w-full h-auto max-h-[80vh] object-contain rounded-lg">
                        </div>
                    </div>
                @endif

                <!-- Enhanced Delete Confirmation Modal -->
                @if ($confirmingGemDeletion && $gemToDelete)
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="cancelDelete">
                        <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4 transform transition-all duration-300 scale-100">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-red-100 rounded-full">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Delete Gem</h3>
                            <p class="text-gray-600 mb-2 text-center">Are you sure you want to delete</p>
                            <p class="text-gray-900 font-semibold mb-6 text-center text-lg">"{{ $gemToDelete->name }}"</p>
                            <p class="text-sm text-gray-500 mb-8 text-center">This action cannot be undone. The gem and its image will be permanently removed from your collection.</p>
                            
                            <div class="flex space-x-4">
                                <button 
                                    wire:click="cancelDelete" 
                                    class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-xl transition-all duration-200 border border-gray-300"
                                >
                                    Cancel
                                </button>
                                <button 
                                    wire:click="deleteGem" 
                                    class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="deleteGem">Delete Forever</span>
                                    <span wire:loading wire:target="deleteGem">Deleting...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success/Error Messages -->
                @if (session()->has('message'))
                    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg z-50 transform transition-all duration-300" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ session('message') }}
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg z-50 transform transition-all duration-300" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>