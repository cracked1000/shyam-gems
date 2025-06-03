<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Shyam Gems')</title>
    
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
        
        .user-badge {
            background: linear-gradient(135deg, #9a8211 0%, #c9a911 100%);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .verification-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
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

    <!-- Email Verification Warning Banner -->
    @auth
        @if(!auth()->user()->hasVerifiedEmail())
            <div id="verificationBanner" class="verification-warning text-white py-3 px-4 shadow-lg relative z-50">
                <div class="container mx-auto flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                        <div>
                            <p class="font-semibold">Email Verification Required</p>
                            <p class="text-sm opacity-90">Please verify your email address to access all features.</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <form action="{{ route('verification.send') }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 flex items-center space-x-2">
                                <i class="fas fa-envelope"></i>
                                <span>Resend Email</span>
                            </button>
                        </form>
                        <button onclick="document.getElementById('verificationBanner').style.display='none'" class="text-white hover:text-yellow-200 p-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endauth

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
                        <!-- Home Link -->
                        <a href="{{ url('/') }}" class="nav-item text-gray-700 hover:text-yellow-600 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center space-x-2 transition-all duration-300">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                        
                        @guest
                            <a href="{{ route('login') }}" class="nav-item btn-primary text-white px-6 py-2.5 rounded-full text-sm font-medium flex items-center space-x-2 ml-4">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <a href="{{ route('seller.register.show') }}" class="nav-item bg-gray-700 hover:bg-gray-800 text-white px-6 py-2.5 rounded-full text-sm font-medium flex items-center space-x-2 transition-all duration-300">
                                <i class="fas fa-user-plus"></i>
                                <span>Register</span>
                            </a>
                        @else
                            @if(auth()->user()->hasVerifiedEmail())
                                <!-- Only show these links for verified users -->
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
                            @else
                                <!-- Show limited options for unverified users -->
                                <div class="flex items-center space-x-2 bg-yellow-50 px-4 py-2 rounded-lg border border-yellow-200">
                                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                    <span class="text-yellow-700 text-sm font-medium">Please verify your email to access features</span>
                                </div>
                            @endif
                        @endguest
                    </div>

                    <!-- User Profile & Logout (Desktop) -->
                    @auth
                        <div class="hidden lg:flex items-center space-x-4">
                            <div class="flex items-center space-x-3">
                                <!-- User Badge -->
                                <div class="flex items-center space-x-3 bg-gradient-to-r from-yellow-50 to-yellow-100 px-4 py-2.5 rounded-full border border-yellow-200">
                                    <div class="user-badge w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-md {{ !auth()->user()->hasVerifiedEmail() ? 'opacity-60' : '' }}">
                                        @if(auth()->user()->role === 'seller')
                                            S
                                        @elseif(auth()->user()->role === 'client')
                                            C
                                        @else
                                            {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->username ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-gray-700 font-medium text-sm">{{ auth()->user()->username ?? auth()->user()->first_name ?? 'User' }}</p>
                                            @if(!auth()->user()->hasVerifiedEmail())
                                                <i class="fas fa-exclamation-triangle text-yellow-500 text-xs" title="Email not verified"></i>
                                            @else
                                                <i class="fas fa-check-circle text-green-500 text-xs" title="Email verified"></i>
                                            @endif
                                        </div>
                                        <p class="text-gray-500 text-xs capitalize">{{ auth()->user()->role ?? 'Member' }}</p>
                                    </div>
                                </div>
                                
                                <!-- Logout Button -->
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="logout-btn text-white px-6 py-2.5 rounded-full text-sm font-medium flex items-center space-x-2" onclick="return confirm('Are you sure you want to logout?')">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
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
                <div class="space-y-3">
                    <!-- Home Link -->
                    <a href="{{ url('/') }}" class="block text-gray-700 hover:text-yellow-600 hover:bg-yellow-50 p-3 rounded-lg transition-all duration-300 flex items-center space-x-3">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    
                    @guest
                        <a href="{{ route('login') }}" class="block w-full btn-primary text-white p-3 rounded-lg text-center font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                        <a href="{{ route('seller.register.show') }}" class="block w-full bg-gray-700 hover:bg-gray-800 text-white p-3 rounded-lg text-center font-medium flex items-center justify-center space-x-2 transition-all duration-300">
                            <i class="fas fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    @else
                        <!-- Mobile User Profile -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="user-badge w-12 h-12 rounded-full flex items-center justify-center font-bold shadow-md {{ !auth()->user()->hasVerifiedEmail() ? 'opacity-60' : '' }}">
                                    @if(auth()->user()->role === 'seller')
                                        S
                                    @elseif(auth()->user()->role === 'client')
                                        C
                                    @else
                                        {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->username ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <p class="font-semibold text-gray-800">{{ auth()->user()->username ?? auth()->user()->first_name ?? 'User' }}</p>
                                        @if(!auth()->user()->hasVerifiedEmail())
                                            <i class="fas fa-exclamation-triangle text-yellow-500 text-sm" title="Email not verified"></i>
                                        @else
                                            <i class="fas fa-check-circle text-green-500 text-sm" title="Email verified"></i>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 capitalize">{{ auth()->user()->role ?? 'Member' }}</p>
                                </div>
                            </div>
                            
                            @if(!auth()->user()->hasVerifiedEmail())
                                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex items-center space-x-2 text-yellow-700">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span class="text-sm font-medium">Email verification required</span>
                                    </div>
                                    <form action="{{ route('verification.send') }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg text-sm font-medium w-full">
                                            Resend Verification Email
                                        </button>
                                    </form>
                                </div>
                            @endif
                            
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full logout-btn text-white p-3 rounded-lg font-medium flex items-center justify-center space-x-2" onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <main class="min-h-screen bg-gray-50">
        @yield('content')
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

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuBtn = event.target.closest('button');
            
            if (mobileMenu && mobileMenu.classList.contains('open')) {
                if (!mobileMenuBtn || !mobileMenuBtn.onclick || mobileMenuBtn.onclick.toString().indexOf('toggleMobileMenu') === -1) {
                    if (!mobileMenu.contains(event.target)) {
                        toggleMobileMenu();
                    }
                }
            }
        });

        // Enhanced notification auto-hide with fade effect
        function hideNotification(element) {
            if (element) {
                element.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                element.style.opacity = '0';
                element.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    element.style.display = 'none';
                }, 500);
            }
        }

        // Auto-hide notifications after 5 seconds
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            hideNotification(successMessage);
            hideNotification(errorMessage);
        }, 5000);

        // Prevent form double submission
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButtons = form.querySelectorAll('button[type="submit"]');
                    submitButtons.forEach(button => {
                        button.disabled = true;
                        button.style.opacity = '0.6';
                        button.style.cursor = 'not-allowed';
                        
                        // Re-enable after 3 seconds as fallback
                        setTimeout(() => {
                            button.disabled = false;
                            button.style.opacity = '1';
                            button.style.cursor = 'pointer';
                        }, 3000);
                    });
                });
            });
        });

        // Enhanced error handling for missing routes
        document.addEventListener('click', function(event) {
            const link = event.target.closest('a');
            if (link && link.href && link.href.includes('undefined')) {
                event.preventDefault();
                console.warn('Invalid route detected:', link.href);
                
                // Show user-friendly message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed top-4 right-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white p-4 rounded-xl shadow-lg z-50 notification-slide';
                errorDiv.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="font-medium">This feature is not yet available.</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-amber-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                document.body.appendChild(errorDiv);
                
                setTimeout(() => {
                    hideNotification(errorDiv);
                }, 4000);
            }
        });

        // Block access to protected features for unverified users
        document.addEventListener('click', function(event) {
            const link = event.target.closest('a');
            if (link && !window.userVerified) {
                const protectedPaths = ['/dashboard', '/feed', '/messages'];
                const href = link.getAttribute('href');
                
                if (protectedPaths.some(path => href && href.includes(path))) {
                    event.preventDefault();
                    
                    // Show verification required message
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'fixed top-4 right-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-4 rounded-xl shadow-lg z-50 notification-slide';
                    warningDiv.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span class="font-medium">Please verify your email address to access this feature.</span>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-yellow-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    document.body.appendChild(warningDiv);
                    
                    setTimeout(() => {
                        hideNotification(warningDiv);
                    }, 4000);
                }
            }
        });

        // Set user verification status
        @auth
            window.userVerified = {{ auth()->user()->hasVerifiedEmail() ? 'true' : 'false' }};
        @else
            window.userVerified = false;
        @endauth
    </script>
    @livewireScripts
</body>
</html>