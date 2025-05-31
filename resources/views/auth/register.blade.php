@extends('layouts.traditional')

@section('title', 'Register')

@section('content')
<div class="min-h-screen bg-gray-100 flex justify-center items-center py-12">
    <div class="bg-white rounded-3xl overflow-hidden shadow-lg flex max-w-4xl w-full mx-4">
        <!-- Left Golden Panel with Black Stripes -->
        <div class="w-1/3 bg-[#9a8211] relative">
            <div class="absolute left-4 top-0 bottom-0 flex space-x-2">
                <div class="w-4 bg-[#231816] rounded-sm"></div>
                <div class="w-4 bg-[#231816] rounded-sm"></div>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="w-2/3 p-8">
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-8">Register for Marketplace</h2>

            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Registration Form -->
            <form method="POST" action="{{ $role === 'seller' ? route('seller.register') : route('client.register') }}" class="space-y-4">
                @csrf

                <!-- First Name Input -->
                <div>
                    <input id="first_name"
                         class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white"
                         type="text"
                         name="first_name"
                         value="{{ old('first_name') }}"
                         placeholder="First Name"
                         required
                         autofocus />
                </div>

                <!-- Last Name Input -->
                <div>
                    <input id="last_name"
                         class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white"
                         type="text"
                         name="last_name"
                         value="{{ old('last_name') }}"
                         placeholder="Last Name"
                         required />
                </div>

                <!-- Email Input -->
                <div>
                    <input id="email"
                         class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white"
                         type="email"
                         name="email"
                         value="{{ old('email') }}"
                         placeholder="Email"
                         required />
                </div>

                <!-- Username Input -->
                <div>
                    <input id="username"
                         class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white"
                         type="text"
                         name="username"
                         value="{{ old('username') }}"
                         placeholder="Username"
                         required />
                </div>

                <!-- Role Selection -->
                <div>
                    <select id="role" 
                          name="role" 
                          class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 bg-white"
                          required>
                        <option value="" disabled {{ old('role') || $role ? '' : 'selected' }}>Select Role</option>
                        <option value="client" {{ (old('role') ?? $role) == 'client' ? 'selected' : '' }}>Client</option>
                        <option value="seller" {{ (old('role') ?? $role) == 'seller' ? 'selected' : '' }}>Seller</option>
                    </select>
                </div>

                <!-- Password Input -->
                <div>
                    <input id="password"
                         class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white"
                         type="password"
                         name="password"
                         placeholder="Password"
                         required
                         autocomplete="new-password" />
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <input id="password_confirmation"
                         class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white"
                         type="password"
                         name="password_confirmation"
                         placeholder="Confirm Password"
                         required
                         autocomplete="new-password" />
                </div>

                <!-- Register Button -->
                <div class="pt-2">
                    <button type="submit"
                         class="w-full bg-[#9a8211] hover:bg-[#8a7310] focus:bg-[#8a7310] active:bg-[#7a6310] py-4 text-white font-medium rounded-xl transition-colors duration-200">
                        Register
                    </button>
                </div>
            </form>

            <p class="text-center mt-6 text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('login.show') }}" class="text-[#00B4D8] hover:underline">Login here</a>
            </p>
        </div>
    </div>
</div>
@endsection