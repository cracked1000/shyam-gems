@extends('layouts.traditional')

@section('title', 'Login')

@section('content')
<div class="min-h-screen bg-gray-100 flex justify-center items-center py-12">
    <div class="bg-white rounded-3xl overflow-hidden shadow-lg flex max-w-2xl w-full mx-4">
        <!-- Left Golden Panel with Black Stripes -->
        <div class="w-1/3 bg-[#9a8211] relative">
            <div class="absolute left-4 top-0 bottom-0 flex space-x-2">
                <div class="w-4 bg-[#231816] rounded-sm"></div>
                <div class="w-4 bg-[#231816] rounded-sm"></div>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="w-2/3 p-8">
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-8">Login to Marketplace</h2>

            
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

            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                
                <div>
                    <input id="email" 
                        class="block w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="Email"
                        required 
                        autofocus />
                </div>

               
                <div>
                    <input id="password" 
                        class="block w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:border-[#9a8211] text-gray-700 placeholder-gray-400 bg-white" 
                        type="password" 
                        name="password" 
                        placeholder="Password"
                        required 
                        autocomplete="current-password" />
                </div>

                
                <div>
                    <button type="submit" 
                        class="w-full bg-[#9a8211] hover:bg-[#8a7310] focus:bg-[#8a7310] active:bg-[#7a6310] py-4 text-white font-medium rounded-xl transition-colors duration-200">
                        Login
                    </button>
                </div>

                @if (Route::has('forgot-password.show'))
                    <div class="text-center">
                        <a class="text-sm text-[#00B4D8] hover:underline" href="{{ route('forgot-password.show') }}">
                            Forgot your password?
                        </a>
                    </div>
                @endif
            </form>

            <p class="text-center mt-6 text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('client.register') }}" class="text-[#00B4D8] hover:underline">Register as Client</a> | 
                <a href="{{ route('seller.register') }}" class="text-[#00B4D8] hover:underline">Register as Seller</a>
            </p>
        </div>
    </div>
</div>
@endsection