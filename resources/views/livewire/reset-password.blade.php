<div>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-stone-50 to-amber-50">
        <div class="fixed inset-0 opacity-3 pointer-events-none">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(154, 130, 17, 0.1) 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div class="w-full max-w-md p-8 bg-white shadow-lg rounded-3xl border border-gray-100">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 mx-auto mb-4">
                    <i class="fas fa-lock-open text-2xl text-amber-700"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Reset Password</h2>
                <p class="text-sm text-gray-600 mt-2">Enter a new password</p>
            </div>

            @if($message)
                <div class="mb-6 text-center text-green-600 bg-green-50 p-3 rounded-2xl">
                    {{ $message }}
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="email" id="email" readonly
                           class="mt-1 w-full px-4 py-2 border border-gray-200 rounded-2xl bg-gray-100 text-gray-900"
                           placeholder="Enter your email">
                    @error('email')
                        <div class="mt-2 text-red-600 text-sm flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" wire:model="password" id="password"
                           class="mt-1 w-full px-4 py-2 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500"
                           placeholder="Enter new password">
                    @error('password')
                        <div class="mt-2 text-red-600 text-sm flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" wire:model="password_confirmation" id="password_confirmation"
                           class="mt-1 w-full px-4 py-2 border border-gray-200 rounded-2xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-300 bg-gray-50/50 text-gray-900 placeholder-gray-500"
                           placeholder="Confirm new password">
                    @error('password_confirmation')
                        <div class="mt-2 text-red-600 text-sm flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white font-semibold py-3 px-6 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center"
                        wire:loading.attr="disabled">
                    <i class="fas fa-check mr-2"></i>
                    Reset Password
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-amber-600 hover:text-amber-700">Back to Login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('show-success', data => {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center`;
                notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${data.message}`;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            });

            Livewire.on('show-error', data => {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center`;
                notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${data.message}`;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            });

            Livewire.on('redirect-to-login', () => {
                setTimeout(() => {
                    window.location.href = '{{ route('login') }}';
                }, 2000); // Delay to show the success