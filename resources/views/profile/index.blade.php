@extends('layouts.app')

@section('title', __('My Profile') . ' - ' . $user->name)

@php
    if (!function_exists('getProfileTierColor')) {
        function getProfileTierColor($tier) {
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
                'D' => 'text-slate-400 border-slate-550/50 bg-slate-900/80 backdrop-blur-xs',
            ];
            return $colors[$tier] ?? 'text-slate-300 border-slate-700 bg-slate-900/80 backdrop-blur-xs';
        }
    }
@endphp

@section('content')
<div class="space-y-8">
    
    <!-- Profile Header Card -->
    <div class="glass border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-violet-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
                <!-- Avatar -->
                <div class="relative group">
                    @if($user->avatar)
                        <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                            class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover ring-4 ring-indigo-500/30 shadow-xl">
                    @else
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-slate-200 dark:bg-slate-900 border border-slate-350 dark:border-slate-800 flex items-center justify-center font-black text-slate-700 dark:text-slate-400 text-3xl sm:text-4xl ring-4 ring-indigo-500/30 shadow-xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <!-- Basic Bio Info -->
                <div class="space-y-2">
                    <div class="flex flex-col sm:flex-row items-center gap-2.5">
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-855 dark:text-white tracking-tight">{{ $user->name }}</h2>
                        @if($highestTier !== '-')
                            <span class="px-2 py-0.5 text-[10px] font-bold border rounded-lg uppercase {{ getProfileTierColor($highestTier) }}">
                                Tier {{ $highestTier }} Max
                            </span>
                        @endif
                    </div>

                    <p class="text-slate-655 dark:text-slate-400 text-sm max-w-lg leading-relaxed">
                        {{ $user->bio ?? __('This adventurer has not written their bio yet.') }}
                    </p>

                    <div class="flex items-center gap-4 text-xs text-slate-450 dark:text-slate-500 justify-center sm:justify-start">
                        <span><i class="fa-solid fa-clock mr-1"></i>{{ __('Join') }} {{ $user->created_at->diffForHumans() }}</span>
                        @if($user->last_login_at)
                            <span><i class="fa-solid fa-right-to-bracket mr-1"></i>{{ __('Active') }} {{ $user->last_login_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Edit Profile Trigger (Only if owner) -->
            @auth
                @if(Auth::id() === $user->id)
                    <button onclick="toggleModal('editProfileModal')"
                        class="shrink-0 px-4 py-2.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-850 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-1.5 self-center md:self-start cursor-pointer shadow-sm">
                        <i class="fa-solid fa-pen-to-square"></i> {{ __('Edit Profile') }}
                    </button>
                @endif
            @endauth
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mt-8 pt-8 border-t border-slate-200 dark:border-slate-900/60 relative z-10">
            <!-- Points -->
            <div class="p-4 bg-white/40 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-900 rounded-2xl text-center shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">{{ __('Total Points') }}</span>
                <p class="text-xl font-black text-amber-500 dark:text-amber-400 mt-1 flex items-center justify-center gap-1"><i class="fa-solid fa-star text-sm"></i>{{ $user->points }}</p>
            </div>
            <!-- Uploads count -->
            <div class="p-4 bg-white/40 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-900 rounded-2xl text-center shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">{{ __('Total Uploads') }}</span>
                <p class="text-xl font-black text-indigo-650 dark:text-indigo-400 mt-1"><i class="fa-solid fa-cube text-sm mr-1"></i>{{ $user->unique_items_count }}</p>
            </div>
            <!-- Likes received count -->
            <div class="p-4 bg-white/40 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-900 rounded-2xl text-center shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">{{ __('Likes Received') }}</span>
                <p class="text-xl font-black text-rose-500 dark:text-rose-455 mt-1"><i class="fa-solid fa-heart text-sm mr-1"></i>{{ $likesReceived }}</p>
            </div>
            <!-- Reposts count -->
            <div class="p-4 bg-white/40 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-900 rounded-2xl text-center shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Reposts</span>
                <p class="text-xl font-black text-violet-605 dark:text-violet-400 mt-1"><i class="fa-solid fa-retweet text-sm mr-1"></i>{{ $user->reposts_count }}</p>
            </div>
            <!-- Comments count -->
            <div class="p-4 bg-white/40 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-900 rounded-2xl text-center shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">{{ __('Comments') }}</span>
                <p class="text-xl font-black text-sky-600 dark:text-sky-400 mt-1"><i class="fa-solid fa-comment text-sm mr-1"></i>{{ $user->comments_count }}</p>
            </div>
            <!-- Logins count -->
            <div class="p-4 bg-white/40 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-900 rounded-2xl text-center shadow-sm">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">{{ __('Visits') }}</span>
                <p class="text-xl font-black text-teal-650 dark:text-teal-400 mt-1"><i class="fa-solid fa-arrow-trend-up text-sm mr-1"></i>{{ $user->login_count }}</p>
            </div>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div>
        <!-- Tabs Header -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-900 mb-6">
            <button onclick="switchTab('uploads-tab', 'reposts-tab', this)"
                class="px-5 py-3 text-sm font-bold border-b-2 border-indigo-505 text-indigo-650 dark:text-indigo-400 focus:outline-none transition-colors cursor-pointer">
                {{ __('Total Uploads') }} ({{ $user->unique_items_count }})
            </button>
            <button onclick="switchTab('reposts-tab', 'uploads-tab', this)"
                class="px-5 py-3 text-sm font-semibold text-slate-505 dark:text-slate-400 border-b-2 border-transparent hover:text-slate-800 dark:hover:text-white focus:outline-none transition-colors cursor-pointer">
                Reposts ({{ $user->reposts->count() }})
            </button>
        </div>

        <!-- Tab 1: Uploads -->
        <div id="uploads-tab" class="tab-content">
            @if($user->uniqueItems->isEmpty())
                <div class="glass border-slate-200 dark:border-slate-900 rounded-2xl p-12 text-center text-slate-550 dark:text-slate-500 text-sm">
                    <i class="fa-solid fa-camera text-2xl mb-2 text-slate-400 dark:text-slate-650"></i>
                    <p>{{ __('No Unique Items Found') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($user->uniqueItems as $item)
                        <div class="rounded-xl glass overflow-hidden border border-slate-200 dark:border-slate-850 hover:border-slate-355 dark:hover:border-slate-750 transition-all flex flex-col h-full hover:-translate-y-1">
                            <div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-950">
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                <span class="absolute top-2.5 left-2.5 px-2 py-0.5 text-[10px] font-black tracking-wider border rounded uppercase {{ getProfileTierColor($item->tier) }}">
                                    {{ $item->tier }}
                                </span>
                            </div>
                            <div class="p-4 flex flex-col justify-between flex-grow">
                                <h3 class="font-bold text-slate-800 dark:text-slate-200 line-clamp-1"><a href="{{ route('items.show', $item) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $item->title }}</a></h3>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 pt-2.5 border-t border-slate-200 dark:border-slate-900">
                                    <span><i class="fa-solid fa-star text-amber-500 dark:text-amber-400 mr-0.5"></i>+{{ $item->points }}</span>
                                    <span><i class="fa-solid fa-heart mr-0.5"></i>{{ $item->likes_count }}</span>
                                    <span><i class="fa-solid fa-comment mr-0.5"></i>{{ $item->comments_count }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tab 2: Reposts -->
        <div id="reposts-tab" class="tab-content hidden">
            @if($user->reposts->isEmpty())
                <div class="glass border-slate-200 dark:border-slate-900 rounded-2xl p-12 text-center text-slate-555 dark:text-slate-500 text-sm">
                    <i class="fa-solid fa-retweet text-2xl mb-2 text-slate-400 dark:text-slate-650"></i>
                    <p>{{ __('No unique items reposted yet.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($user->reposts as $repost)
                        @if($repost->uniqueItem)
                            <div class="rounded-xl glass overflow-hidden border border-slate-200 dark:border-slate-850 hover:border-slate-355 dark:hover:border-slate-750 transition-all flex flex-col h-full hover:-translate-y-1">
                                <div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-950">
                                    <img src="{{ asset('storage/' . $repost->uniqueItem->image_path) }}" alt="{{ $repost->uniqueItem->title }}" class="w-full h-full object-cover">
                                    <span class="absolute top-2.5 left-2.5 px-2 py-0.5 text-[10px] font-black tracking-wider border rounded uppercase {{ getProfileTierColor($repost->uniqueItem->tier) }}">
                                        {{ $repost->uniqueItem->tier }}
                                    </span>
                                    <!-- Repost indicator -->
                                    <div class="absolute bottom-2.5 right-2.5 px-2 py-0.5 bg-indigo-600/90 text-white rounded text-[9px] font-bold flex items-center gap-1 shadow">
                                        <i class="fa-solid fa-retweet"></i> Reposted
                                    </div>
                                </div>
                                <div class="p-4 flex flex-col justify-between flex-grow">
                                    <div class="space-y-1">
                                        <h3 class="font-bold text-slate-800 dark:text-slate-200 line-clamp-1"><a href="{{ route('items.show', $repost->uniqueItem) }}" class="hover:text-indigo-650 dark:hover:text-indigo-400 transition-colors">{{ $repost->uniqueItem->title }}</a></h3>
                                        <p class="text-[10px] text-slate-500">{{ __('Found by') }}: {{ $repost->uniqueItem->user->name ?? 'Anonim' }}</p>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] text-slate-550 mt-3 pt-2.5 border-t border-slate-200 dark:border-slate-900">
                                        <span><i class="fa-solid fa-star text-amber-500 dark:text-amber-400 mr-0.5"></i>+{{ $repost->uniqueItem->points }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Edit Profile Modal (Only if owner) -->
@auth
    @if(Auth::id() === $user->id)
        <div id="editProfileModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm hidden">
            <div class="w-full max-w-md p-6 rounded-2xl glass border border-slate-200 dark:border-slate-800 shadow-2xl relative m-4">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200 dark:border-slate-900">
                    <h3 class="text-lg font-bold text-slate-850 dark:text-white"><i class="fa-solid fa-pen-to-square text-indigo-550 dark:text-indigo-400 mr-1.5"></i>{{ __('Edit Profile') }}</h3>
                    <button onclick="toggleModal('editProfileModal')" class="text-slate-400 hover:text-slate-850 dark:hover:text-white transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Avatar Upload -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">{{ __('Avatar') }}</label>
                        <div class="flex items-center gap-4 flex-wrap sm:flex-nowrap">
                            @if($user->avatar)
                                <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-xl object-cover ring-2 ring-indigo-500/20">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-slate-200 dark:bg-slate-900 border border-slate-250 dark:border-slate-800 flex items-center justify-center font-bold text-slate-700 dark:text-slate-400 text-lg">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <input type="file" name="avatar" id="avatar" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-200 dark:file:bg-slate-900 file:text-slate-700 dark:file:text-slate-300 file:hover:bg-slate-300 dark:file:hover:bg-slate-855 cursor-pointer">
                                <p class="text-[10px] text-slate-450 dark:text-slate-500 mt-1">{{ __('Supports JPEG, PNG, WEBP. Max 2MB.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Name Input -->
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">{{ __('Full Name') }}</label>
                        <input type="text" name="name" id="name" value="{{ $user->name }}" required
                            class="block w-full px-4 py-2.5 bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner">
                    </div>

                    <!-- Bio Input -->
                    <div>
                        <label for="bio" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">{{ __('Bio') }}</label>
                        <textarea name="bio" id="bio" rows="3"
                            class="block w-full px-4 py-2.5 bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner"
                            placeholder="{{ __('Describe yourself or your unique item hunting adventures...') }}">{{ $user->bio }}</textarea>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-905">
                        <button type="button" onclick="toggleModal('editProfileModal')" class="px-4 py-2 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-850 text-slate-600 hover:text-slate-805 dark:text-slate-450 dark:hover:text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold transition-all cursor-pointer">
                            {{ __('Save') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
@endauth

@endsection

@section('scripts')
<script>
    function switchTab(showTabId, hideTabId, activeBtn) {
        document.getElementById(showTabId).classList.remove('hidden');
        document.getElementById(hideTabId).classList.add('hidden');
        
        // Update button styles
        const buttons = activeBtn.parentElement.children;
        for (let btn of buttons) {
            btn.classList.remove('border-indigo-505', 'text-indigo-650', 'dark:text-indigo-400', 'font-bold');
            btn.classList.add('border-transparent', 'text-slate-500', 'dark:text-slate-400');
        }
        activeBtn.classList.remove('border-transparent', 'text-slate-505', 'dark:text-slate-400');
        activeBtn.classList.add('border-indigo-505', 'text-indigo-650', 'dark:text-indigo-400', 'font-bold');
    }

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }
</script>
@endsection
