@extends('layouts.app')

@section('title', __('Search'))

@php
    if (!function_exists('getSearchTierColor')) {
        function getSearchTierColor($tier) {
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
                'B' => 'text-emerald-450 border-emerald-500/50 bg-slate-900/80 backdrop-blur-xs',
                'C+' => 'text-orange-400 border-orange-400/50 bg-slate-900/80 backdrop-blur-xs',
                'C' => 'text-orange-400 border-orange-505/50 bg-slate-900/80 backdrop-blur-xs',
                'D+' => 'text-slate-300 border-slate-400/50 bg-slate-900/80 backdrop-blur-xs',
                'D' => 'text-slate-400 border-slate-550/50 bg-slate-900/80 backdrop-blur-xs',
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
                'B' => 'border-emerald-505/10 dark:border-emerald-550/8 hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-[0_0_15px_rgba(16,185,129,0.1)]',
                'C+' => 'border-orange-400/20 dark:border-orange-400/15 hover:border-orange-400 dark:hover:border-orange-400 hover:shadow-[0_0_15px_rgba(251,146,60,0.15)]',
                'C' => 'border-orange-505/10 dark:border-orange-505/8 hover:border-orange-500 dark:hover:border-orange-500 hover:shadow-[0_0_15px_rgba(249,115,22,0.1)]',
            ];
            return $styles[$tier] ?? 'border-slate-200 dark:border-slate-850 hover:border-slate-350 dark:hover:border-slate-750';
        }
    }
@endphp

@section('content')
<div class="space-y-8">

    <!-- Search Input Banner -->
    <div class="glass border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-16 -left-16 w-60 h-60 bg-indigo-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-16 -right-16 w-60 h-60 bg-violet-500/5 rounded-full blur-3xl"></div>

        <form action="{{ route('search') }}" method="GET" class="relative z-10 max-w-2xl mx-auto space-y-4">
            <h1 class="text-xl sm:text-2xl font-black text-slate-850 dark:text-white text-center">
                <i class="fa-solid fa-magnifying-glass text-indigo-505 mr-1"></i> {{ __('Global Search') }}
            </h1>
            
            <div class="flex gap-2">
                <input type="text" name="q" value="{{ $query }}" required
                    placeholder="{{ __('Search items or adventurers...') }}"
                    class="w-full px-5 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-inner text-sm">
                
                <button type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-600/20 transition-all cursor-pointer">
                    {{ __('Search') }}
                </button>
            </div>
        </form>
    </div>

    @if($query)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Users Found (Col 4) -->
            <div class="lg:col-span-4 space-y-5">
                <h3 class="text-base font-bold text-slate-805 dark:text-white tracking-wide border-b border-slate-200 dark:border-slate-900 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-user text-indigo-550 dark:text-indigo-400"></i> {{ __('Adventurers') }} ({{ $users->count() }})
                </h3>

                @if($users->isEmpty())
                    <p class="text-xs text-slate-500 py-6 text-center bg-white/20 dark:bg-slate-900/10 rounded-2xl border border-slate-200 dark:border-slate-850">{{ __('No matching adventurers found.') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($users as $user)
                            <a href="{{ route('profile.show', $user->uuid) }}" class="flex items-center gap-3 p-3 rounded-2xl glass border-slate-200 dark:border-slate-850 hover:border-slate-350 dark:hover:border-slate-750 transition-all shadow-sm hover:-translate-y-0.5 group">
                                @if($user->avatar)
                                    <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-11 h-11 rounded-xl object-cover">
                                @else
                                    <div class="w-11 h-11 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0 flex-grow">
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate group-hover:text-indigo-650 dark:group-hover:text-indigo-400 transition-colors">{{ $user->name }}</h4>
                                    <p class="text-[10px] text-slate-500 font-bold text-amber-500"><i class="fa-solid fa-star text-[9px] mr-0.5"></i>{{ $user->points }} Pts</p>
                                    @if($user->bio)
                                        <p class="text-[10px] text-slate-550 dark:text-slate-400 truncate mt-0.5">{{ $user->bio }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Items Found (Col 8) -->
            <div class="lg:col-span-8 space-y-5">
                <h3 class="text-base font-bold text-slate-805 dark:text-white tracking-wide border-b border-slate-200 dark:border-slate-900 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-cube text-indigo-550 dark:text-indigo-400"></i> {{ __('Unique Items') }} ({{ $items->count() }})
                </h3>

                @if($items->isEmpty())
                    <p class="text-xs text-slate-500 py-6 text-center bg-white/20 dark:bg-slate-900/10 rounded-2xl border border-slate-200 dark:border-slate-850">{{ __('No matching unique items found.') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($items as $item)
                            <div class="rounded-2xl glass overflow-hidden border transition-all flex flex-col h-full hover:-translate-y-1 group relative {{ getCardTierStyle($item->tier) }}">
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
                                <div class="relative aspect-video overflow-hidden bg-slate-100 dark:bg-slate-950 flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 text-[9px] font-black tracking-widest border rounded-xl uppercase {{ getSearchTierColor($item->tier) }}">
                                        {{ $item->tier }}
                                    </span>
                                </div>
                                <div class="p-4 flex flex-col justify-between flex-grow space-y-2">
                                    <h4 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-indigo-650 dark:group-hover:text-indigo-400 transition-colors line-clamp-1">
                                        <a href="{{ route('items.show', $item->uuid) }}">{{ $item->title }}</a>
                                    </h4>
                                    @if($item->location_name)
                                        <p class="text-[10px] text-slate-500 truncate"><i class="fa-solid fa-location-dot mr-1 text-indigo-500"></i>{{ $item->location_name }}</p>
                                    @endif
                                    <div class="pt-2 border-t border-slate-150 dark:border-slate-900 flex items-center justify-between text-[10px] text-slate-550">
                                        <span>{{ __('Found by') }}: <strong>{{ $item->user->name }}</strong></span>
                                        <span class="text-amber-500 font-bold"><i class="fa-solid fa-star text-[9px]"></i> +{{ $item->points }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    @endif

</div>
@endsection
