<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Account Settings</h2>

            {{-- Success Messages --}}
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif

            {{-- Error Messages --}}
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Password Change Section -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Change Password
                </h3>
                
                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    {{-- Current Password --}}
                    <div>
                        <x-jet-label for="current_password" value="Current Password" class="font-medium text-gray-700" />
                        <x-jet-input 
                            id="current_password" 
                            class="block mt-1 w-full {{ $errors->has('current_password') ? 'border-red-500' : '' }}" 
                            type="password" 
                            wire:model="current_password" 
                            placeholder="Enter your current password"
                            required 
                        />
                        @error('current_password') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <x-jet-label for="new_password" value="New Password" class="font-medium text-gray-700" />
                        <x-jet-input 
                            id="new_password" 
                            class="block mt-1 w-full {{ $errors->has('new_password') ? 'border-red-500' : '' }}" 
                            type="password" 
                            wire:model="new_password" 
                            placeholder="Enter your new password"
                            required 
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            Password must be at least 8 characters and include uppercase, lowercase, numbers, and symbols.
                        </p>
                        @error('new_password') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-jet-label for="new_password_confirmation" value="Confirm New Password" class="font-medium text-gray-700" />
                        <x-jet-input 
                            id="new_password_confirmation" 
                            class="block mt-1 w-full {{ $errors->has('new_password') ? 'border-red-500' : '' }}" 
                            type="password" 
                            wire:model="new_password_confirmation" 
                            placeholder="Confirm your new password"
                            required 
                        />
                    </div>

                    <x-jet-button 
                        type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50" 
                        wire:loading.attr="disabled"
                        wire:target="updatePassword"
                    >
                        <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                        <span wire:loading wire:target="updatePassword" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </span>
                    </x-jet-button>
                </form>
            </div>

            <!-- 2FA Section -->
            <div class="p-6 bg-gray-50 rounded-lg">
                <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Two-Factor Authentication
                </h3>

                @if ($user->google2fa_enabled)
                    {{-- 2FA Enabled State --}}
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded-md">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-green-800 font-medium">Two-Factor Authentication is enabled</p>
                        </div>
                        <p class="text-green-700 text-sm mt-1">Your account is protected with an additional layer of security.</p>
                    </div>
                    
                    <button 
                        wire:click="disable2FA" 
                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors disabled:opacity-50 flex items-center"
                        wire:loading.attr="disabled"
                        wire:target="disable2FA"
                        onclick="return confirm('Are you sure you want to disable Two-Factor Authentication? This will make your account less secure.')"
                    >
                        <span wire:loading.remove wire:target="disable2FA">Disable 2FA</span>
                        <span wire:loading wire:target="disable2FA" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Disabling...
                        </span>
                    </button>
                @else
                    {{-- 2FA Disabled State --}}
                    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 rounded-md">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-yellow-800 font-medium">Two-Factor Authentication is disabled</p>
                        </div>
                        <p class="text-yellow-700 text-sm mt-1">Enable 2FA to add an extra layer of security to your account.</p>
                    </div>

                    @if ($showQrCode && $qrCodeSvg)
                        {{-- QR Code Display --}}
                        <div class="mb-6 p-4 bg-white border rounded-lg">
                            <h4 class="font-medium text-gray-800 mb-3">Step 1: Scan QR Code</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.):
                            </p>
                            
                            <div class="flex justify-center mb-4">
                                <div class="p-4 bg-white border rounded-lg">
                                    {!! $qrCodeSvg !!}
                                </div>
                            </div>

                            <h4 class="font-medium text-gray-800 mb-3">Step 2: Enter Verification Code</h4>
                            <div class="space-y-4">
                                <div>
                                    <x-jet-label for="code" value="6-Digit Code from Authenticator App" class="font-medium text-gray-700" />
                                    <x-jet-input 
                                        id="code" 
                                        class="block mt-1 w-full {{ $errors->has('code') ? 'border-red-500' : '' }}" 
                                        type="text" 
                                        wire:model="code" 
                                        placeholder="123456"
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        required 
                                    />
                                    @error('code') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>

                                <div class="flex space-x-3">
                                    <x-jet-button 
                                        wire:click="enable2FA" 
                                        class="bg-green-600 hover:bg-green-700 disabled:opacity-50"
                                        wire:loading.attr="disabled"
                                        wire:target="enable2FA"
                                    >
                                        <span wire:loading.remove wire:target="enable2FA">Enable 2FA</span>
                                        <span wire:loading wire:target="enable2FA" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Enabling...
                                        </span>
                                    </x-jet-button>

                                    <button 
                                        wire:click="cancelQrCode" 
                                        class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                                        type="button"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Generate QR Code Button --}}
                        <x-jet-button 
                            wire:click="generateSecret" 
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                            wire:loading.attr="disabled"
                            wire:target="generateSecret"
                        >
                            <span wire:loading.remove wire:target="generateSecret">Setup Two-Factor Authentication</span>
                            <span wire:loading wire:target="generateSecret" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Generating...
                            </span>
                        </x-jet-button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>