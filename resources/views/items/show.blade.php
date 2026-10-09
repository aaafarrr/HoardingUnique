@extends('layouts.app')

@section('title', $item->title)

@section('styles')
<style>
    #detailMap {
        height: 250px;
        border-radius: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .dark #detailMap {
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
</style>
@endsection

@php
    if (!function_exists('getTierColor')) {
        function getTierColor($tier) {
            $colors = [
                'SSS+' => 'text-amber-600 dark:text-amber-400 border-amber-400 bg-amber-400/5 shadow-[0_0_20px_rgba(250,204,21,0.25)]',
                'SSS' => 'text-amber-600 dark:text-amber-500 border-amber-500 bg-amber-500/5 shadow-[0_0_15px_rgba(234,179,8,0.15)]',
                'SS+' => 'text-purple-600 dark:text-purple-400 border-purple-400 bg-purple-400/5 shadow-[0_0_20px_rgba(168,85,247,0.25)]',
                'SS' => 'text-purple-600 dark:text-purple-500 border-purple-500 bg-purple-505/5 shadow-[0_0_15px_rgba(147,51,234,0.15)]',
                'S+' => 'text-rose-600 dark:text-rose-450 border-rose-450 bg-rose-450/5 shadow-[0_0_20px_rgba(251,113,133,0.25)]',
                'S' => 'text-rose-600 dark:text-rose-500 border-rose-500 bg-rose-500/5 shadow-[0_0_15px_rgba(244,63,94,0.15)]',
                'A+' => 'text-sky-600 dark:text-sky-400 border-sky-400 bg-sky-400/5',
                'A' => 'text-sky-600 dark:text-sky-505 border-sky-505 bg-sky-500/5',
                'B+' => 'text-emerald-600 dark:text-emerald-400 border-emerald-400 bg-emerald-400/5',
                'B' => 'text-emerald-600 dark:text-emerald-500 border-emerald-500 bg-emerald-550/5',
                'C+' => 'text-orange-650 dark:text-orange-400 border-orange-400 bg-orange-400/5',
                'C' => 'text-orange-600 dark:text-orange-505 border-orange-505 bg-orange-500/5',
                'D+' => 'text-slate-500 dark:text-slate-400 border-slate-400 bg-slate-400/5',
                'D' => 'text-slate-600 dark:text-slate-550 border-slate-550 bg-slate-550/5',
            ];
            return $colors[$tier] ?? 'text-slate-500 dark:text-slate-300 border-slate-700 bg-slate-700/5';
        }
    }

    $shareText = __('I found a :tier Tier unique item with :points Points on Hoarding Unique! Check it out:', ['tier' => $item->tier, 'points' => $item->points]);
    $itemUrl = route('items.show', $item);
@endphp

@section('content')
<div class="space-y-8">
    
    <!-- Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Image Display & Actions -->
        <div class="lg:col-span-7 space-y-6">
            @php
                $detailCardGlow = [
                    'SSS+' => 'border-amber-400/40 dark:border-amber-450/30 shadow-[0_0_40px_rgba(250,204,21,0.2)]',
                    'SSS' => 'border-amber-505/30 dark:border-amber-505/20 shadow-[0_0_30px_rgba(234,179,8,0.15)]',
                    'SS+' => 'border-purple-400/40 dark:border-purple-450/30 shadow-[0_0_40px_rgba(168,85,247,0.2)]',
                    'SS' => 'border-purple-500/30 dark:border-purple-500/20 shadow-[0_0_30px_rgba(147,51,234,0.15)]',
                    'S+' => 'border-rose-455/40 dark:border-rose-455/30 shadow-[0_0_40px_rgba(251,113,133,0.2)]',
                    'S' => 'border-rose-500/30 dark:border-rose-500/20 shadow-[0_0_30px_rgba(244,63,94,0.15)]',
                ][$item->tier] ?? 'border-slate-200 dark:border-slate-800 shadow-2xl';
            @endphp
            <div class="relative rounded-3xl overflow-hidden glass p-4 {{ $detailCardGlow }}">
                
                <!-- Glowing dominant color backdrop -->
                <div class="absolute inset-0 opacity-20 blur-3xl rounded-3xl pointer-events-none scale-90"
                    style="background: radial-gradient(circle, {{ $item->dominant_color }} 0%, transparent 70%);">
                </div>

                <div class="relative rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-950/80 aspect-square flex items-center justify-center">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                        class="w-full h-full object-contain">
                </div>
            </div>

            <!-- Social Action Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 glass border-slate-200 dark:border-slate-850 rounded-2xl shadow-xl">
                <div class="flex items-center gap-6">
                    <!-- Like Form -->
                    <form action="{{ route('items.like', $item) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 text-sm font-semibold transition-all hover:scale-105 cursor-pointer {{ $isLiked ? 'text-rose-500' : 'text-slate-500 hover:text-rose-455 dark:text-slate-400 dark:hover:text-rose-400' }}">
                            <i class="fa-solid fa-heart text-lg {{ $isLiked ? 'fa-beat-once' : '' }}"></i>
                            <span>{{ $item->likes_count }} {{ __('Likes') }}</span>
                        </button>
                    </form>

                    <!-- Repost Form -->
                    <form action="{{ route('items.repost', $item) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 text-sm font-semibold transition-all hover:scale-105 cursor-pointer {{ $isReposted ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 hover:text-indigo-650 dark:text-slate-400 dark:hover:text-indigo-400' }}">
                            <i class="fa-solid fa-retweet text-lg"></i>
                            <span>{{ $item->reposts_count }} {{ __('Reposts') }}</span>
                        </button>
                    </form>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    {{ __('Uploaded on') }} {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                </div>
            </div>
        </div>

        <!-- Right: Meta, Description & Geolocation -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Tiering & Poin Panel -->
            <div class="glass border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-500 dark:text-slate-450 uppercase font-bold tracking-widest">{{ __('Rarity Tier') }}</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="px-4 py-1.5 text-lg font-black tracking-widest border rounded-xl uppercase {{ getTierColor($item->tier) }}">
                                TIER {{ $item->tier }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <span class="text-xs text-slate-500 dark:text-slate-450 uppercase font-bold tracking-widest">{{ __('Points Earned') }}</span>
                        <div class="mt-1 text-2xl font-black text-amber-600 dark:text-amber-400 flex items-center gap-1.5 justify-end">
                            <i class="fa-solid fa-star text-lg"></i> +{{ $item->points }} {{ __('Pts') }}
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200/80 dark:border-slate-900 flex items-center gap-3">
                    <span class="text-xs text-slate-550 dark:text-slate-450">{{ __('Dominant Color') }}:</span>
                    <span class="w-5 h-5 rounded-lg border border-slate-250 dark:border-slate-800 shadow" style="background-color: {{ $item->dominant_color }}"></span>
                    <span class="text-xs font-mono text-slate-650 dark:text-slate-300">{{ strtoupper($item->dominant_color) }}</span>
                </div>
            </div>

            <!-- Item Main details -->
            <div class="glass rounded-3xl p-6 space-y-4 {{ $detailCardGlow }}">
                <div class="space-y-1">
                    <span class="text-[10px] text-indigo-650 dark:text-indigo-400 font-bold uppercase tracking-wider font-mono">{{ __('Finder Info') }}</span>
                    <a href="{{ route('profile.show', $item->user) }}" class="flex items-center gap-3 group mt-1">
                        @if($item->user->avatar)
                            <img src="{{ str_starts_with($item->user->avatar, 'http') ? $item->user->avatar : asset('storage/' . $item->user->avatar) }}" alt="{{ $item->user->name }}" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-0">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-600 dark:text-slate-350">
                                {{ strtoupper(substr($item->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h4 class="text-sm font-bold text-slate-805 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">{{ $item->user->name }}</h4>
                            <p class="text-xs text-slate-500">{{ __('Point Rank') }}: {{ $item->user->points }} {{ __('Pts') }}</p>
                        </div>
                    </a>
                </div>

                <div class="pt-4 border-t border-slate-200/80 dark:border-slate-900/60 space-y-2">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-wide">{{ $item->title }}</h3>
                    <p class="text-sm text-slate-650 dark:text-slate-350 leading-relaxed whitespace-pre-line">{{ $item->description ?? '' }}</p>
                </div>
            </div>

            <!-- Geolocation tracking -->
            @if($item->latitude && $item->longitude)
                <div class="glass border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-550 dark:text-slate-400">{{ __('Location of Discovery') }}</h4>
                        <span class="text-[10px] font-semibold text-slate-500 truncate max-w-[60%]"><i class="fa-solid fa-location-dot text-indigo-500 dark:text-indigo-400"></i> {{ $item->location_name }}</span>
                    </div>

                    <div id="detailMap" class="shadow"></div>
                </div>
            @endif

            <!-- Social Media Sharing Panel -->
            <div class="glass border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-550 dark:text-slate-400">{{ __('Share This Discovery') }}</h4>
                
                <div class="grid grid-cols-5 gap-2.5">
                    <!-- Twitter / X -->
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($shareText) }}&url={{ urlencode($itemUrl) }}" target="_blank"
                        class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 border border-slate-200 dark:border-slate-800 rounded-xl text-center text-slate-800 dark:text-white text-base hover:-translate-y-0.5 transition-all cursor-pointer"
                        title="Twitter / X">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($itemUrl) }}" target="_blank"
                        class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 border border-slate-200 dark:border-slate-800 rounded-xl text-center text-blue-650 dark:text-blue-400 text-base hover:-translate-y-0.5 transition-all cursor-pointer"
                        title="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($shareText . ' ' . $itemUrl) }}" target="_blank"
                        class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 border border-slate-200 dark:border-slate-800 rounded-xl text-center text-emerald-600 dark:text-emerald-400 text-base hover:-translate-y-0.5 transition-all cursor-pointer"
                        title="WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <!-- Telegram -->
                    <a href="https://t.me/share/url?url={{ urlencode($itemUrl) }}&text={{ urlencode($shareText) }}" target="_blank"
                        class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 border border-slate-200 dark:border-slate-800 rounded-xl text-center text-sky-550 dark:text-sky-400 text-base hover:-translate-y-0.5 transition-all cursor-pointer"
                        title="Telegram">
                        <i class="fa-brands fa-telegram"></i>
                    </a>
                    <!-- Copy Link -->
                    <div class="relative">
                        <button onclick="copyItemLink()"
                            class="w-full p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 border border-slate-200 dark:border-slate-800 rounded-xl text-center text-indigo-600 dark:text-indigo-400 text-base hover:-translate-y-0.5 transition-all cursor-pointer"
                            title="{{ __('Copy Link') }}">
                            <i class="fa-solid fa-link"></i>
                        </button>
                        <span id="copyTooltip" class="absolute -top-9 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-md opacity-0 invisible transition-all duration-250 shadow pointer-events-none whitespace-nowrap">
                            {{ __('Link copied!') }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Comments Section -->
    <div class="max-w-4xl glass border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        <h3 class="text-lg font-bold text-slate-850 dark:text-white tracking-wide">
            {{ __('Comments') }} ({{ $item->comments_count }})
        </h3>

        <!-- Comment Form (Requires Auth) -->
        @auth
            <form action="{{ route('items.comment', $item) }}" method="POST" class="flex gap-4 items-start">
                @csrf
                @if(Auth::user()->avatar)
                    <img src="{{ str_starts_with(Auth::user()->avatar, 'http') ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-xl object-cover shrink-0 ring-1 ring-slate-200 dark:ring-0">
                @else
                    <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-600 dark:text-slate-450 shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif

                <div class="flex-grow space-y-2">
                    <textarea name="content" rows="2" required
                        class="block w-full px-4 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner"
                        placeholder="{{ __('Write comment...') }}"></textarea>
                    
                    @error('content')
                        <p class="text-rose-500 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition-all shadow-md shadow-indigo-600/20 cursor-pointer">
                            {{ __('Send Comment') }}
                        </button>
                    </div>
                </div>
            </form>
        @else
            <!-- Guest Warning -->
            <div class="p-5 rounded-2xl border border-dashed border-slate-250 dark:border-slate-800 bg-slate-100/30 dark:bg-slate-900/10 text-center space-y-3">
                <i class="fa-solid fa-lock text-slate-400 dark:text-slate-600 text-lg"></i>
                <p class="text-xs text-slate-550 dark:text-slate-400 font-semibold">{{ __('Please login to interact') }}</p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition-all shadow-sm">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350 text-xs font-bold rounded-xl transition-all">{{ __('Register') }}</a>
                </div>
            </div>
        @endauth

        <!-- Comments List -->
        <div class="space-y-4 pt-4 border-t border-slate-200/80 dark:border-slate-900/80">
            @if($item->comments->isEmpty())
                <p class="text-center text-sm text-slate-500 py-4">{{ __('No comments yet') }}</p>
            @else
                @foreach($item->comments as $comment)
                    <div class="flex gap-3 items-start text-sm">
                        <a href="{{ route('profile.show', $comment->user) }}">
                            @if($comment->user->avatar)
                                <img src="{{ str_starts_with($comment->user->avatar, 'http') ? $comment->user->avatar : asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}" class="w-9 h-9 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-0">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-slate-200 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-600 dark:text-slate-400 text-xs">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>
                        
                        <div class="flex-grow p-4 rounded-2xl bg-white dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-850/50 space-y-1.5 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <a href="{{ route('profile.show', $comment->user) }}" class="font-bold text-slate-855 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $comment->user->name }}
                                </a>
                                <span class="text-[10px] text-slate-450 dark:text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
@if($item->latitude && $item->longitude)
<script>
    let map, marker;
    document.addEventListener('DOMContentLoaded', () => {
        const lat = {{ $item->latitude }};
        const lng = {{ $item->longitude }};
        const locationName = "{{ $item->location_name ?? __('Location of Discovery') }}";
        
        // Initialize Map
        map = L.map('detailMap', {
            zoomControl: true,
            scrollWheelZoom: false,
            doubleClickZoom: false
        }).setView([lat, lng], 14);

        updateMapTileLayer();

        // Add Marker
        marker = L.marker([lat, lng]).addTo(map)
            .bindPopup(`<strong>${locationName}</strong>`)
            .openPopup();

        // Re-layer on theme change dynamically
        window.addEventListener('theme-changed', () => {
            updateMapTileLayer();
        });
    });

    function updateMapTileLayer() {
        if (!map) return;
        
        // Clear existing tile layers if any
        map.eachLayer((layer) => {
            if (layer instanceof L.TileLayer) {
                map.removeLayer(layer);
            }
        });

        const isDark = document.documentElement.classList.contains('dark');
        const tileUrl = isDark 
            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
            : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

        L.tileLayer(tileUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 20
        }).addTo(map);
    }
</script>
@endif
<script>
    function copyItemLink() {
        navigator.clipboard.writeText("{{ route('items.show', $item) }}").then(() => {
            const tooltip = document.getElementById('copyTooltip');
            tooltip.classList.remove('opacity-0', 'invisible');
            tooltip.classList.add('opacity-100', 'visible');
            setTimeout(() => {
                tooltip.classList.remove('opacity-100', 'visible');
                tooltip.classList.add('opacity-0', 'invisible');
            }, 2000);
        });
    }
</script>
@endsection
