@extends('layouts.app')

@section('title', __('Register'))

@section('content')
<div class="flex items-center justify-center min-h-[75vh]">
    <div class="w-full max-w-md p-8 rounded-2xl glass shadow-2xl relative overflow-hidden border border-slate-200 dark:border-slate-800">
        
        <!-- Background light effect -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-violet-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>

        <div class="text-center mb-8 relative z-10">
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-850 dark:text-white">
                {{ __('Create New Account') }}
            </h2>
            <p class="text-slate-505 dark:text-slate-400 text-sm mt-2">{{ __('Register now and publish your unique discoveries.') }}</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5 relative z-10">
            @csrf

            <!-- Name Input -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">{{ __('Full Name') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                        class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner"
                        placeholder="{{ __('Your Name') }}">
                </div>
                @error('name')
                    <p class="text-rose-605 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner"
                        placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="text-rose-605 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner"
                        placeholder="{{ __('Minimum 8 Characters') }}">
                </div>
                @error('password')
                    <p class="text-rose-605 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">{{ __('Repeat Password') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-lock-open"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner"
                        placeholder="{{ __('Repeat Password') }}">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-all hover:shadow-indigo-600/40 hover:-translate-y-0.5 active:translate-y-0 text-sm cursor-pointer">
                {{ __('Register Now') }}
            </button>
        </form>

        <!-- Divider -->
        <div class="relative flex items-center justify-center my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
            </div>
            <span class="relative px-3 bg-slate-50 dark:bg-slate-950/85 text-[10px] font-bold text-slate-450 dark:text-slate-550 uppercase tracking-widest">Or</span>
        </div>

        <!-- Google OAuth Button -->
        <div class="space-y-3 relative z-10">
            <a href="{{ route('auth.google') }}"
                class="w-full flex items-center justify-center gap-2.5 py-3 px-4 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-white font-semibold border border-slate-200 dark:border-slate-800 rounded-xl transition-all text-sm shadow-sm">
                <i class="fa-brands fa-google text-rose-500"></i>
                {{ __('Register with Google') }}
            </a>
        </div>

        <!-- Sign In Link -->
        <div class="text-center mt-8 text-sm text-slate-505 relative z-10">
            {{ __('Already have an account?') }} 
            <a href="{{ route('login') }}" class="text-indigo-650 dark:text-indigo-400 hover:underline font-semibold transition-colors">{{ __('Log in here') }}</a>
        </div>

    </div>
</div>
@endsection
