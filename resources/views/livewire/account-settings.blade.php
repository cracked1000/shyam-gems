<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h2 class="text-2xl font-bold mb-4">Account Settings</h2>

            @if (session('message'))
                <div class="mb-4 text-green-600">{{ session('message') }}</div>
            @endif

            @error('current_password') <div class="mb-4 text-red-600">{{ $message }}</div> @enderror
            @error('code') <div class="mb-4 text-red-600">{{ $message }}</div> @enderror

            <!-- Password Change Section -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-4">Change Password</h3>
                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <x-jet-label for="current_password" value="Current Password" />
                        <x-jet-input id="current_password" class="block mt-1 w-full" type="password" wire:model="current_password" required />
                    </div>
                    <div>
                        <x-jet-label for="new_password" value="New Password" />
                        <x-jet-input id="new_password" class="block mt-1 w-full" type="password" wire:model="new_password" required />
                    </div>
                    <div>
                        <x-jet-label for="new_password_confirmation" value="Confirm New Password" />
                        <x-jet-input id="new_password_confirmation" class="block mt-1 w-full" type="password" wire:model="new_password_confirmation" required />
                    </div>
                    <x-jet-button type="submit">Update Password</x-jet-button>
                </form>
            </div>

            <!-- 2FA Section -->
            <div>
                <h3 class="text-xl font-semibold mb-4">Two-Factor Authentication</h3>
                @if ($user->google2fa_enabled)
                    <p class="mb-4 text-gray-700">Two-Factor Authentication is enabled.</p>
                    <button wire:click="disable2FA" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Disable 2FA</button>
                @else
                    <p class="mb-4 text-gray-700">Two-Factor Authentication is disabled. Enable it by scanning the QR code below with your authenticator app (e.g., Google Authenticator).</p>
                    @if ($qrCodeSvg)
                        <div class="mb-4">{!! $qrCodeSvg !!}</div>
                        <div>
                            <x-jet-label for="code" value="Enter Code from Authenticator" />
                            <x-jet-input id="code" class="block mt-1 w-full" type="text" wire:model="code" required />
                        </div>
                        <x-jet-button wire:click="enable2FA">Enable 2FA</x-jet-button>
                    @else
                        <x-jet-button wire:click="generateSecret">Generate QR Code</x-jet-button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>