<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Shyam Gems - Home' }}</title>
    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .modal-hidden { display: none; }
        .modal-visible { display: flex; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        
        .gold-gradient { 
            background: linear-gradient(135deg, #9a8211 0%, #c9a911 50%, #9a8211 100%); 
        }
        
        .nav-item {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #d4af37, #f4d03f);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-item:hover::before {
            width: 80%;
        }
        
        .profile-dropdown {
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .profile-dropdown.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }
        
        .mobile-menu {
            transform: translateY(-100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .mobile-menu.open {
            transform: translateY(0);
        }
        
        .notification-slide {
            animation: slideInRight 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #9a8211 0%, #c9a911 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #8a7410 0%, #b8980f 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(154, 130, 17, 0.3);
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(154, 130, 17, 0.1);
        }
    </style>
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 text-gray-800 font-poppins">
    
    <!-- Success/Error Notifications -->
    @if (session('success'))
        <div id="successMessage" class="fixed top-4 right-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-4 rounded-xl shadow-lg z-50 notification-slide">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle"></i>
                <span class="font-medium">{{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="ml-2 text-white hover:text-emerald-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="errorMessage" class="fixed top-4 right-4 bg-gradient-to-r from-red-500 to-red-600 text-white p-4 rounded-xl shadow-lg z-50 notification-slide">
            <div class="flex items-center space-x-3">
                <i class="fas fa-exclamation-circle"></i>
                <span class="font-medium">{{ session('error') }}</span>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="ml-2 text-white hover:text-red-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Header -->
    <header class="relative">
        <!-- Top Bar with Logo -->
        <div class="gold-gradient text-white shadow-lg">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center py-4">
                    <div class="flex items-center space-x-4">
                        <div class="relative group">
                            @if(file_exists(public_path('Images/g1.png')))
                                <img src="{{ asset('Images/g1.png') }}" alt="Shyam Gems Logo" class="h-12 w-12 transition-transform duration-300 group-hover:scale-110">
                            @else
                                <div class="h-12 w-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-gem text-2xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 rounded-full transition-opacity duration-300"></div>
                        </div>
                        <div class="text-center">
                            <h1 class="text-3xl sm:text-4xl font-playfair font-bold tracking-wide">SHYAM GEMS</h1>
                            <p class="text-sm opacity-90 font-light hidden sm:block">Premium Jewelry Collection</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Bar -->
        <nav class="glass-effect sticky top-0 z-40 shadow-md">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center space-x-1">
                        @guest
                            <a href="{{ route('login') }}" class="nav-item btn-primary text-white px-6 py-2.5 rounded-full text-sm font-medium flex items-center space-x-2">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="{{ route('seller.register.show') }}" class="nav-item bg-gray-700 hover:bg-gray-800 text-white px-6 py-2.5 rounded-full text-sm font-medium flex items-center space-x-2 transition-all duration-300">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        @else
                            <div class="flex items-center space-x-1">
                                <a href="{{ route('home') }}" class="nav-item text-gray-700 hover:text-yellow-600 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center space-x-2 transition-all duration-300">
                                    <i class="fas fa-home"></i>
                                    <span>Home</span>
                                </a>
                                <a href="{{ route('dashboard') }}" class="nav-item text-gray-700 hover:text-yellow-600 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center space-x-2 transition-all duration-300">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                                </a>
                                @if(Route::has('feed'))
                                    <a href="{{ route('feed') }}" class="nav-item text-gray-700 hover:text-yellow-600 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center space-x-2 transition-all duration-300">
                                        <i class="fas fa-rss"></i>
                                        <span>Feed</span>
                                    </a>
                                @endif
                                @if(Route::has('messages.index'))
                                    <a href="{{ route('messages.index') }}" class="nav-item text-gray-700 hover:text-yellow-600 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center space-x-2 transition-all duration-300 relative">
                                        <i class="fas fa-envelope"></i>
                                        <span>Messages</span>
                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                                    </a>
                                @endif
                            </div>
                        @endguest
                    </div>

                    <!-- User Profile & Logout (Desktop) -->
                    @auth
                        <div class="hidden lg:flex items-center space-x-4">
                            <div class="relative">
                                <button onclick="toggleProfileDropdown()" class="flex items-center space-x-3 bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 px-4 py-2.5 rounded-full border border-yellow-200 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                                    <div class="flex items-center space-x-2">
                                        @if(Auth::user()->profile_photo_path && \Illuminate\Support\Facades\Storage::exists(Auth::user()->profile_photo_path))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url(Auth::user()->profile_photo_path) }}" alt="Profile Photo" class="h-8 w-8 rounded-full object-cover border-2 border-yellow-300">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                @if(Auth::user()->role === 'seller')
                                                    S
                                                @elseif(Auth::user()->role === 'client')
                                                    C
                                                @else
                                                    {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                                @endif
                                            </div>
                                        @endif
                                        <div class="text-left">
                                            <p class="text-gray-700 font-medium text-sm">{{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }}</p>
                                            <p class="text-gray-500 text-xs capitalize">{{ Auth::user()->role ?? 'Member' }}</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </button>
                                
                                <!-- Profile Dropdown -->
                                <div id="profileDropdown" class="profile-dropdown absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50">
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }} {{ Auth::user()->last_name ?? '' }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>
                                    @if(Route::has('profile.edit-settings'))
                                        <a href="{{ route('profile.edit-settings') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                            <i class="fas fa-user mr-3 text-gray-400"></i>
                                            <span>Profile Settings</span>
                                        </a>
                                    @endif
                                    @if(Route::has('account.edit'))
                                        <a href="{{ route('account.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                            <i class="fas fa-cog mr-3 text-gray-400"></i>
                                            <span>Account Settings</span>
                                        </a>
                                    @endif
                                    <div class="border-t border-gray-100 mt-2 pt-2">
                                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                                <i class="fas fa-sign-out-alt mr-3"></i>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-600 hover:text-yellow-600 transition-colors duration-300 p-2">
                        <i id="mobileMenuIcon" class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="mobile-menu lg:hidden absolute top-full left-0 right-0 glass-effect shadow-2xl z-30">
            <div class="container mx-auto px-4 py-6">
                @guest
                    <div class="space-y-3">
                        <a href="{{ route('login') }}" class="block w-full btn-primary text-white p-3 rounded-lg text-center font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                        <a href="{{ route('seller.register.show') }}" class="block w-full bg-gray-700 hover:bg-gray-800 text-white p-3 rounded-lg text-center font-medium flex items-center justify-center space-x-2 transition-all duration-300">
                            <i class="fas fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    </div>
                @else
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" class="block text-gray-700 hover:text-yellow-600 hover:bg-yellow-50 p-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:text-yellow-600 hover:bg-yellow-50 p-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        @if(Route::has('feed'))
                            <a href="{{ route('feed') }}" class="block text-gray-700 hover:text-yellow-600 hover:bg-yellow-50 p-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                                <i class="fas fa-rss"></i>
                                <span>Feed</span>
                            </a>
                        @endif
                        @if(Route::has('messages.index'))
                            <a href="{{ route('messages.index') }}" class="block text-gray-700 hover:text-yellow-600 hover:bg-yellow-50 p-3 rounded-lg transition-all duration-300 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-envelope"></i>
                                    <span>Messages</span>
                                </div>
                                <span class="bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                            </a>
                        @endif
                    </div>
                    
                    <!-- Mobile User Profile -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center space-x-3 mb-4">
                            @if(Auth::user()->profile_photo_path && \Illuminate\Support\Facades\Storage::exists(Auth::user()->profile_photo_path))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url(Auth::user()->profile_photo_path) }}" alt="Profile Photo" class="h-12 w-12 rounded-full object-cover border-2 border-yellow-300">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                    @if(Auth::user()->role === 'seller')
                                        S
                                    @elseif(Auth::user()->role === 'client')
                                        C
                                    @else
                                        {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ Auth::user()->first_name ?? Auth::user()->name ?? 'User' }}</p>
                                <p class="text-sm text-gray-600 capitalize">{{ Auth::user()->role ?? 'Member' }}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            @if(Route::has('profile.edit-settings'))
                                <a href="{{ route('profile.edit-settings') }}" class="block text-gray-700 hover:text-yellow-600 hover:bg-yellow-50 p-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                                    <i class="fas fa-user"></i>
                                    <span>Profile Settings</span>
                                </a>
                            @endif
                            @if(Route::has('account.edit'))
                                <a href="{{ route('account.edit') }}" class="block text-gray-700 hover:text-yellow-600 hover:bg-yellow-50 p-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                                    <i class="fas fa-cog"></i>
                                    <span>Account Settings</span>
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 p-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <main class="min-h-screen bg-gray-50">
        {{ $slot }}
    </main>

    <script>
        // Mobile menu functionality
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuIcon = document.getElementById('mobileMenuIcon');
            
            if (mobileMenu && mobileMenuIcon) {
                mobileMenu.classList.toggle('open');
                
                if (mobileMenu.classList.contains('open')) {
                    mobileMenuIcon.classList.remove('fa-bars');
                    mobileMenuIcon.classList.add('fa-times');
                } else {
                    mobileMenuIcon.classList.remove('fa-times');
                    mobileMenuIcon.classList.add('fa-bars');
                }
            }
        }

        // Profile dropdown functionality
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const profileButton = event.target.closest('button');
            
            if (dropdown && (!profileButton || !profileButton.onclick || profileButton.onclick.toString().indexOf('toggleProfileDropdown') === -1)) {
                dropdown.classList.remove('show');
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuBtn = event.target.closest('button');
            
            if (mobileMenu && mobileMenu.classList.contains('open')) {
                if (!mobileMenuBtn || mobileMenuBtn.onclick.toString().indexOf('toggleMobileMenu') === -1) {
                    if (!mobileMenu.contains(event.target)) {
                        toggleMobileMenu();
                    }
                }
            }
        });

        // Auto-hide notifications
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }, 5000);
    </script>
    @livewireScripts
</body>
</html>