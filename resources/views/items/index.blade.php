@extends('layouts.app')

@section('title', __('Unique Items Feed'))

@php
    if (!function_exists('getTierColorBadge')) {
        function getTierColorBadge($tier) {
            $colors = [
                'SSS+' => 'text-amber-400 border-amber-400/60 bg-slate-900/80 backdrop-blur-xs shadow-[0_0_15px_rgba(250,204,21,0.25)]',
                'SSS' => 'text-amber-400 border-amber-500/50 bg-slate-900/80 backdrop-blur-xs',
                'SS+' => 'text-purple-400 border-purple-400/60 bg-slate-900/80 backdrop-blur-xs shadow-[0_0_15px_rgba(168,85,247,0.25)]',
                'SS' => 'text-purple-400 border-purple-550/50 bg-slate-900/80 backdrop-blur-xs',
                'S+' => 'text-rose-455 border-rose-455/60 bg-slate-900/80 backdrop-blur-xs shadow-[0_0_15px_rgba(251,113,133,0.25)]',
                'S' => 'text-rose-400 border-rose-500/50 bg-slate-900/80 backdrop-blur-xs',
                'A+' => 'text-sky-400 border-sky-400/50 bg-slate-900/80 backdrop-blur-xs',
                'A' => 'text-sky-400 border-sky-505/50 bg-slate-900/80 backdrop-blur-xs',
                'B+' => 'text-emerald-400 border-emerald-400/50 bg-slate-900/80 backdrop-blur-xs',
                'B' => 'text-emerald-400 border-emerald-500/50 bg-slate-900/80 backdrop-blur-xs',
                'C+' => 'text-orange-400 border-orange-400/50 bg-slate-900/80 backdrop-blur-xs',
                'C' => 'text-orange-400 border-orange-505/50 bg-slate-900/80 backdrop-blur-xs',
                'D+' => 'text-slate-300 border-slate-400/50 bg-slate-900/80 backdrop-blur-xs',
                'D' => 'text-slate-400 border-slate-500/50 bg-slate-900/80 backdrop-blur-xs',
            ];
            return $colors[$tier] ?? 'text-slate-300 border-slate-700 bg-slate-900/80 backdrop-blur-xs';
        }
    }

    if (!function_exists('getCardTierStyle')) {
        function getCardTierStyle($tier) {
            $styles = [
                'SSS+' => 'border-amber-400/40 dark:border-amber-450/30 hover:border-amber-400 dark:hover:border-amber-400 shadow-[0_0_20px_rgba(250,204,21,0.02)] hover:shadow-[0_0_30px_rgba(250,204,21,0.35)]',
                'SSS' => 'border-amber-500/20 dark:border-amber-505/15 hover:border-amber-500 dark:hover:border-amber-500 shadow-[0_0_20px_rgba(234,179,8,0.02)] hover:shadow-[0_0_25px_rgba(234,179,8,0.25)]',
                'SS+' => 'border-purple-400/40 dark:border-purple-450/30 hover:border-purple-400 dark:hover:border-purple-400 shadow-[0_0_20px_rgba(168,85,247,0.02)] hover:shadow-[0_0_30px_rgba(168,85,247,0.35)]',
                'SS' => 'border-purple-505/20 dark:border-purple-505/15 hover:border-purple-500 dark:hover:border-purple-500 shadow-[0_0_20px_rgba(147,51,234,0.02)] hover:shadow-[0_0_25px_rgba(147,51,234,0.25)]',
                'S+' => 'border-rose-455/40 dark:border-rose-455/30 hover:border-rose-455 dark:hover:border-rose-455 shadow-[0_0_20px_rgba(251,113,133,0.02)] hover:shadow-[0_0_30px_rgba(251,113,133,0.35)]',
                'S' => 'border-rose-500/20 dark:border-rose-500/15 hover:border-rose-500 dark:hover:border-rose-500 shadow-[0_0_20px_rgba(244,63,94,0.02)] hover:shadow-[0_0_25px_rgba(244,63,94,0.25)]',
                'A+' => 'border-sky-400/20 dark:border-sky-400/15 hover:border-sky-400 dark:hover:border-sky-400 hover:shadow-[0_0_15px_rgba(56,189,248,0.15)]',
                'A' => 'border-sky-505/10 dark:border-sky-550/8 hover:border-sky-500 dark:hover:border-sky-500 hover:shadow-[0_0_15px_rgba(14,165,233,0.1)]',
                'B+' => 'border-emerald-400/20 dark:border-emerald-400/15 hover:border-emerald-400 dark:hover:border-emerald-400 hover:shadow-[0_0_15px_rgba(52,211,153,0.15)]',
                'B' => 'border-emerald-500/10 dark:border-emerald-550/8 hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-[0_0_15px_rgba(16,185,129,0.1)]',
                'C+' => 'border-orange-400/20 dark:border-orange-400/15 hover:border-orange-400 dark:hover:border-orange-400 hover:shadow-[0_0_15px_rgba(251,146,60,0.15)]',
                'C' => 'border-orange-505/10 dark:border-orange-505/8 hover:border-orange-500 dark:hover:border-orange-500 hover:shadow-[0_0_15px_rgba(249,115,22,0.1)]',
            ];
            return $styles[$tier] ?? 'border-slate-200 dark:border-slate-850 hover:border-slate-350 dark:hover:border-slate-750';
        }
    }
@endphp

@section('content')
<div class="space-y-8">

    <!-- Header Banner -->
    <div class="relative rounded-3xl overflow-hidden glass p-6 sm:p-12 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <!-- Abstract gradient shapes -->
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-violet-550/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-2xl space-y-4">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-none">
                {{ __('Find Unique Items') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-lg leading-relaxed">
                {{ __('Feed description') }}
            </p>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="glass border-slate-200 dark:border-slate-850 rounded-2xl p-4 shadow-md">
        <form action="{{ route('items.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <!-- Search Text -->
            <div class="flex-grow relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 dark:text-slate-500">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('Search unique items...') }}"
                    class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-505 focus:border-transparent transition-all text-sm">
            </div>

            <!-- Tier Filter -->
            <div class="w-full md:w-48">
                <select name="tier"
                    class="w-full px-3 py-2.5 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-505 transition-all text-sm cursor-pointer">
                    <option value="">{{ __('All Tiers') }}</option>
                    @foreach(['SSS+', 'SSS', 'SS+', 'SS', 'S+', 'S', 'A+', 'A', 'B+', 'B', 'C+', 'C', 'D+', 'D'] as $t)
                        <option value="{{ $t }}" {{ request('tier') === $t ? 'selected' : '' }}>Tier {{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-grow md:flex-grow-0 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl transition-all shadow-md shadow-indigo-600/20 cursor-pointer">
                    {{ __('Search') }}
                </button>
                @if(request('search') || request('tier'))
                    <a href="{{ route('items.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-350 rounded-xl transition-colors flex items-center justify-center cursor-pointer"
                        title="{{ __('Clear Filters') }}">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Items Grid -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-slate-850 dark:text-white tracking-wide">
                {{ __('Total') }}: {{ $items->total() }} {{ __('Items') }}
            </h2>
        </div>

        @if($items->isEmpty())
            <div class="glass border-slate-200 dark:border-slate-900 rounded-3xl p-12 text-center max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto text-slate-450 dark:text-slate-600 border border-slate-200 dark:border-slate-850">
                    <i class="fa-solid fa-cube text-2xl"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">{{ __('No Unique Items Found') }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ __('No items match search') }}</p>
                </div>
                @if(request('search') || request('tier'))
                    <div class="pt-2">
                        <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm">
                            {{ __('Back to Feed') }}
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($items as $item)
                    <div class="rounded-2xl glass overflow-hidden border transition-all flex flex-col h-full hover:-translate-y-1.5 group relative {{ getCardTierStyle($item->tier) }}">
                        <!-- Corner Ribbon for High Tiers -->
                        @if(in_array($item->tier, ['SSS+', 'SSS']))
                            <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden pointer-events-none z-30">
                                <div class="absolute transform rotate-45 bg-gradient-to-r from-amber-300 to-amber-500 text-slate-950 text-[7px] font-black text-center py-1.5 w-24 -right-6 top-3 shadow-lg uppercase tracking-widest">
                                    LEGEND
                                </div>
                            </div>
                        @elseif(in_array($item->tier, ['SS+', 'SS']))
                            <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden pointer-events-none z-30">
                                <div class="absolute transform rotate-45 bg-gradient-to-r from-purple-400 to-indigo-650 text-white text-[7px] font-black text-center py-1.5 w-24 -right-6 top-3 shadow-lg uppercase tracking-widest">
                                    EPIC
                                </div>
                            </div>
                        @elseif(in_array($item->tier, ['S+', 'S']))
                            <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden pointer-events-none z-30">
                                <div class="absolute transform rotate-45 bg-gradient-to-r from-rose-455 to-rose-650 text-white text-[7px] font-black text-center py-1.5 w-24 -right-6 top-3 shadow-lg uppercase tracking-widest">
                                    RARE
                                </div>
                            </div>
                        @endif
                        
                        <!-- Image Container with Tier overlay -->
                        <div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-950/80 flex items-center justify-center">
                            <!-- Glowing color drop -->
                            <div class="absolute inset-0 opacity-10 blur-xl scale-75 group-hover:scale-95 transition-transform"
                                style="background: radial-gradient(circle, {{ $item->dominant_color }} 0%, transparent 70%);">
                            </div>
                            
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                                class="w-full h-full object-cover relative z-10 transition-transform duration-300 group-hover:scale-102">
                            
                            <!-- Tier badge -->
                            <span class="absolute top-3 left-3 px-3 py-1 text-[10px] font-black tracking-widest border rounded-xl uppercase z-20 {{ getTierColorBadge($item->tier) }}">
                                {{ $item->tier }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 flex flex-col justify-between flex-grow space-y-3 relative z-20">
                            <div class="space-y-1.5">
                                <span class="text-[10px] text-indigo-650 dark:text-indigo-400 font-semibold tracking-wide flex items-center gap-1">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span class="truncate max-w-[150px]">{{ $item->location_name ?? __('Location Hidden') }}</span>
                                </span>

                                <h3 class="font-bold text-slate-850 dark:text-slate-100 line-clamp-1 leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    <a href="{{ route('items.show', $item) }}">{{ $item->title }}</a>
                                </h3>
                                
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $item->description ?? '...' }}
                                </p>
                            </div>

                            <!-- Card Footer -->
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-900 flex items-center justify-between">
                                <a href="{{ route('profile.show', $item->user) }}" class="flex items-center gap-2 group/user">
                                    @if($item->user->avatar)
                                        <img src="{{ str_starts_with($item->user->avatar, 'http') ? $item->user->avatar : asset('storage/' . $item->user->avatar) }}" alt="{{ $item->user->name }}"
                                            class="w-6 h-6 rounded-lg object-cover">
                                    @else
                                        <img src="https://api.dicebear.com/9.x/initials/svg?seed={{ urlencode($item->user->name) }}" alt="{{ $item->user->name }}"
                                            class="w-6 h-6 rounded-lg object-cover">
                                    @endif
                                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 group-hover/user:text-indigo-600 transition-colors max-w-[80px] truncate">{{ $item->user->name }}</span>
                                </a>

                                <div class="flex items-center gap-2.5 text-[10px] text-slate-500 font-semibold">
                                    <span class="text-amber-600 dark:text-amber-400 flex items-center gap-0.5"><i class="fa-solid fa-star"></i> +{{ $item->points }}</span>
                                    <span class="flex items-center gap-0.5"><i class="fa-solid fa-heart"></i> {{ $item->likes_count }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
