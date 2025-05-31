<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-[#9a8211] to-[#b8951a] px-8 py-12">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">Welcome to Your Dashboard</h1>
                        <p class="text-[#f5f1e8] text-lg">Discover and explore precious gems</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            @if($this->user && $this->user->profile_photo_path)
                                <img src="{{ $this->user->profile_photo_url }}" alt="Profile" class="w-20 h-20 rounded-full object-cover border-4 border-white/30">
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

        <div class="max-w-4xl mx-auto">
            <!-- Profile Section -->
            <div>
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <!-- Profile Header -->
                    <div class="text-center mb-8">
                        <div class="relative inline-block">
                            @if($this->user && $this->user->profile_photo_path)
                                <img src="{{ $this->user->profile_photo_url }}" alt="Profile" class="w-28 h-28 rounded-full object-cover mx-auto border-4 border-[#9a8211]/20 shadow-lg">
                            @else
                                <div class="w-28 h-28 bg-gradient-to-br from-[#9a8211]/20 to-[#9a8211]/10 rounded-full flex items-center justify-center mx-auto shadow-lg">
                                    <svg class="w-14 h-14 text-[#9a8211]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-400 rounded-full border-4 border-white shadow-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mt-4">
                            {{ $this->user->first_name ?? 'Valued' }} {{ $this->user->last_name ?? 'Client' }}
                        </h2>
                        <p class="text-[#9a8211] font-medium text-lg"></p>
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-[#9a8211]/10 to-[#9a8211]/20 text-[#9a8211] mt-2">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verified Client
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl p-4 border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-[#9a8211] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Information
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-100">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#9a8211]/20 to-[#9a8211]/10 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-[#9a8211]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Full Name</p>
                                        <p class="text-gray-900 font-medium">
                                            {{ $this->user->first_name ?? 'Not set' }} {{ $this->user->last_name ?? '' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-100">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-blue-500/10 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Username</p>
                                        <p class="text-gray-900 font-medium">{{ $this->user->username ?? 'Not set' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-100">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500/20 to-green-500/10 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Email Address</p>
                                        <p class="text-gray-900 font-medium">{{ $this->user->email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-100">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500/20 to-purple-500/10 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Phone Number</p>
                                        <p class="text-gray-900 font-medium">{{ $this->user->telephone ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 mt-6">
                            <button class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#9a8211] to-[#b8951a] text-white font-semibold rounded-xl hover:from-[#8a7310] hover:to-[#a8851a] transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Profile
                            </button>
                            
                            <button class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 font-semibold rounded-xl hover:from-gray-200 hover:to-gray-300 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Account Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>