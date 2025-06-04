<div class="min-h-screen bg-gradient-to-br from-slate-50 via-stone-50 to-amber-50 p-6">
    <!-- Subtle Background Pattern -->
    <div class="fixed inset-0 opacity-3 pointer-events-none">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(154, 130, 17, 0.1) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-2xl shadow-lg z-50 flex items-center animate-slide-in">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto space-y-8">
        

        @if($user)
            <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border-2 border-amber-200 flex items-center justify-center">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile" class="w-full h-full rounded-2xl object-cover">
                        @else
                            <span class="text-2xl font-bold text-amber-800">
                                {{ strtoupper(substr($user->first_name ?? $user->username ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}
                        </h1>
                        <p class="text-lg text-gray-600">{{ '@' . $user->username }}</p>
                        <p class="text-sm text-gray-500 capitalize">{{ $user->role ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="bg-gray-50 p-4 rounded-2xl">
                        <p class="text-gray-600"><i class="fas fa-envelope mr-2"></i> {{ $user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl">
                        <p class="text-gray-600"><i class="fas fa-phone mr-2"></i> {{ $user->telephone ?? 'N/A' }}</p> <!-- Updated from telephone -->
                    </div>
                </div>
            </div>

            <!-- Seller Gallery Section -->
            @if($user->role === 'seller' && $gems->isNotEmpty())
                <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100 mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Seller Gallery</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($gems as $gem)
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200">
                                <img src="{{ asset('storage/' . $gem->image) }}" alt="{{ $gem->name }}" class="w-full h-48 object-cover rounded-lg mb-2" onerror="this.src='https://via.placeholder.com/500x300?text=No+Image+Available';">
                                <h3 class="text-lg font-semibold">{{ $gem->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ $gem->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($user->role === 'seller' && $gems->isEmpty())
                <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100 mt-8 text-center">
                    <p class="text-gray-600">No gems available in the gallery.</p>
                   
                   
                </div>
            @endif
        @else
            <div class="bg-white shadow-lg rounded-3xl p-8 border border-gray-100 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">User Not Found</h2>
                <p class="text-gray-600">The user you're looking for does not exist or an error occurred.</p>
            </div>
        @endif
    </div>

    <!-- Styles -->
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
    </style>

    <!-- JavaScript for Flash Messages -->
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
        });
    </script>