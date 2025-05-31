<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>

            @if (session('message'))
                <div class="mb-4 text-green-600">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="updateProfile" class="space-y-6">
                <div>
                    <x-jet-label for="first_name" value="First Name" />
                    <x-jet-input id="first_name" class="block mt-1 w-full" type="text" wire:model="first_name" required />
                    @error('first_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-jet-label for="last_name" value="Last Name" />
                    <x-jet-input id="last_name" class="block mt-1 w-full" type="text" wire:model="last_name" required />
                    @error('last_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-jet-label for="email" value="Email" />
                    <x-jet-input id="email" class="block mt-1 w-full" type="email" wire:model="email" required />
                    @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-jet-label for="username" value="Username" />
                    <x-jet-input id="username" class="block mt-1 w-full" type="text" wire:model="username" required />
                    @error('username') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-jet-label for="telephone" value="Telephone" />
                    <x-jet-input id="telephone" class="block mt-1 w-full" type="text" wire:model="telephone" />
                    @error('telephone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-jet-label for="bio" value="Bio" />
                    <textarea id="bio" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" wire:model="bio"></textarea>
                    @error('bio') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-jet-label for="experience" value="Experience in Gem Field" />
                    <textarea id="experience" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" wire:model="experience"></textarea>
                    @error('experience') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-jet-label for="profile_photo" value="Profile Photo" />
                    <input type="file" id="profile_photo" wire:model="profile_photo" class="block mt-1 w-full text-gray-700">
                    @error('profile_photo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    @if ($user->profile_photo_path)
                        <div class="mt-2">
                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Profile Photo" class="h-20 w-20 rounded-full object-cover border-2 border-yellow-300">
                        </div>
                    @endif
                </div>

                <div>
                    <x-jet-button type="submit">
                        Update Profile
                    </x-jet-button>
                </div>
            </form>
        </div>
    </div>
</div>