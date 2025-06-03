<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl border border-gray-100">
        <div class="px-6 py-8">
            <!-- Header -->
            <div class="border-b border-gray-200 pb-6 mb-8">
                <h2 class="text-3xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Edit Profile
                </h2>
                <p class="mt-2 text-gray-600">Update your personal information and preferences</p>
            </div>

            <!-- Flash Messages -->
            @if (session('message'))
                <div class="mb-6 p-4 rounded-lg border {{ session('message_type') === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-green-50 border-green-200 text-green-800' }}">
                    <div class="flex items-center">
                        @if(session('message_type') === 'error')
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="updateProfile" class="space-y-8">
                <!-- Profile Photo Section -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Profile Photo
                    </h3>
                    
                    <div class="flex items-center space-x-6">
                        @if ($user->profile_photo_path)
                            <div class="relative">
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="Profile Photo" 
                                     class="h-24 w-24 rounded-full object-cover border-4 border-yellow-300 shadow-lg">
                                <button type="button" 
                                        wire:click="removePhoto"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center border-4 border-gray-300">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <x-jet-label for="profile_photo" value="Choose New Photo" class="mb-2" />
                            <input type="file" 
                                   id="profile_photo" 
                                   wire:model="profile_photo" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 file:cursor-pointer cursor-pointer border border-gray-300 rounded-lg">
                            @error('profile_photo') 
                                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-jet-label for="first_name" value="First Name" class="text-gray-700 font-medium" />
                        <x-jet-input id="first_name" 
                                     class="block mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                                     type="text" 
                                     wire:model.live="first_name" 
                                     placeholder="Enter your first name"
                                     required />
                        @error('first_name') 
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <x-jet-label for="last_name" value="Last Name" class="text-gray-700 font-medium" />
                        <x-jet-input id="last_name" 
                                     class="block mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                                     type="text" 
                                     wire:model.live="last_name" 
                                     placeholder="Enter your last name"
                                     required />
                        @error('last_name') 
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <x-jet-label for="email" value="Email Address" class="text-gray-700 font-medium" />
                        <x-jet-input id="email" 
                                     class="block mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                                     type="email" 
                                     wire:model.live="email" 
                                     placeholder="Enter your email address"
                                     required />
                        @error('email') 
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <x-jet-label for="username" value="Username" class="text-gray-700 font-medium" />
                        <x-jet-input id="username" 
                                     class="block mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                                     type="text" 
                                     wire:model.live="username" 
                                     placeholder="Choose a unique username"
                                     required />
                        @error('username') 
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <x-jet-label for="telephone" value="Phone Number" class="text-gray-700 font-medium" />
                        <x-jet-input id="telephone" 
                                     class="block mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500" 
                                     type="tel" 
                                     wire:model.live="telephone" 
                                     placeholder="Enter your phone number (optional)" />
                        @error('telephone') 
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <!-- Bio and Experience -->
                <div class="space-y-6">
                    <div>
                        <x-jet-label for="bio" value="Bio" class="text-gray-700 font-medium" />
                        <textarea id="bio" 
                                  class="block mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500 min-h-[100px] resize-y" 
                                  wire:model.live="bio"
                                  placeholder="Tell us about yourself..."
                                  rows="4"></textarea>
                        <div class="mt-1 text-sm text-gray-500 text-right">
                            {{ strlen($bio ?? '') }}/1000 characters
                        </div>
                        @error('bio') 
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <x-jet-label for="experience" value="Experience in Gem Field" class="text-gray-700 font-medium" />
                        <textarea id="experience" 
                                  class="block mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500 min-h-[100px] resize-y" 
                                  wire:model.live="experience"
                                  placeholder="Describe your experience with gems..."
                                  rows="4"></textarea>
                        <div class="mt-1 text-sm text-gray-500 text-right">
                            {{ strlen($experience ?? '') }}/1000 characters
                        </div>
                        @error('experience') 
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex justify-end space-x-4">
                        <button type="button" 
                                onclick="window.history.back()"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        
                        <x-jet-button type="submit" 
                                      class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                      :disabled="$isLoading">
                            @if($isLoading)
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            @else
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Profile
                            @endif
                        </x-jet-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>