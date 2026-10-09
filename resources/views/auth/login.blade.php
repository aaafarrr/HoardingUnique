@extends('layouts.app')

@section('title', __('Login'))

@section('content')
<div class="flex items-center justify-center min-h-[75vh]">
    <div class="w-full max-w-md p-8 rounded-2xl glass shadow-2xl relative overflow-hidden border border-slate-200 dark:border-slate-800">
        
        <!-- Background light effect -->
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-violet-500/10 rounded-full blur-3xl"></div>

        <div class="text-center mb-8 relative z-10">
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-850 dark:text-white">
                {{ __('Login') }}
            </h2>
            <p class="text-slate-550 dark:text-slate-400 text-sm mt-2">{{ __('Log in to Hoarding Unique to start sharing unique items.') }}</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-5 relative z-10">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="block w-full pl-10 pr-3 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner"
                        placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="text-rose-600 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
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
                        placeholder="••••••••">
                </div>
                @error('password')
                    <p class="text-rose-600 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer text-slate-500 dark:text-slate-400 hover:text-slate-805 dark:hover:text-slate-350 transition-colors">
                    <input type="checkbox" name="remember" class="w-4.5 h-4.5 rounded bg-slate-100 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-indigo-650 dark:text-indigo-400 focus:ring-indigo-500/30 cursor-pointer">
                    <span>{{ __('Remember Me') }}</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-all hover:shadow-indigo-600/40 hover:-translate-y-0.5 active:translate-y-0 text-sm cursor-pointer">
                {{ __('Log In Now') }}
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
                {{ __('Log in with Google') }}
            </a>

            <!-- Simulator for Local Dev -->
            <a href="{{ route('auth.google.mock') }}"
                class="w-full flex items-center justify-center gap-2.5 py-2.5 px-4 bg-indigo-50 dark:bg-indigo-950/20 hover:bg-indigo-100 dark:hover:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 font-semibold border border-indigo-100 dark:border-indigo-900/30 rounded-xl transition-all text-xs shadow-sm">
                <i class="fa-solid fa-laptop-code"></i>
                {{ __('Simulate Google Login (Local Testing)') }}
            </a>
        </div>

        <!-- Sign Up Link -->
        <div class="text-center mt-8 text-sm text-slate-500 relative z-10">
            {{ __("Don't have an account?") }} 
            <a href="{{ route('register') }}" class="text-indigo-655 dark:text-indigo-400 hover:underline font-semibold transition-colors">{{ __('Register here') }}</a>
        </div>

    </div>
</div>
@endsection
