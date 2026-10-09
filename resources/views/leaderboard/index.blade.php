@extends('layouts.app')

@section('title', __('Leaderboard'))

@section('content')
<div class="space-y-8">

    <!-- Header Banner -->
    <div class="relative rounded-3xl overflow-hidden glass p-8 sm:p-12 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-xl mx-auto text-center space-y-3">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                <i class="fa-solid fa-trophy text-amber-550 dark:text-amber-400 mr-2"></i> {{ __('Leaderboard') }}
            </h1>
            <p class="text-slate-550 dark:text-slate-400 text-sm">
                {{ __('Appreciation for the most active unique item finders in the Hoarding Unique community.') }}
            </p>
        </div>
    </div>

    <!-- Category Tabs Selector -->
    <div class="flex flex-nowrap gap-2 pb-2.5 border-b border-slate-200 dark:border-slate-900 overflow-x-auto scroll-smooth">
        @foreach([
            'points' => ['label' => __('Total Points'), 'icon' => 'fa-star text-amber-500 dark:text-amber-400'],
            'uploads' => ['label' => __('Total Uploads'), 'icon' => 'fa-cube text-indigo-650 dark:text-indigo-400'],
            'likes' => ['label' => __('Likes Received'), 'icon' => 'fa-heart text-rose-500'],
            'comments' => ['label' => __('Comments'), 'icon' => 'fa-comment text-sky-500 dark:text-sky-400'],
            'logins' => ['label' => __('Visits'), 'icon' => 'fa-right-to-bracket text-teal-650 dark:text-teal-400'],
            'active' => ['label' => __('Activity Score'), 'icon' => 'fa-fire text-orange-500'],
        ] as $catKey => $catMeta)
            <a href="{{ route('leaderboard', ['category' => $catKey]) }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold border transition-all flex items-center gap-1.5 shrink-0 {{ $category === $catKey ? 'bg-indigo-600/10 text-indigo-605 text-indigo-600 dark:text-indigo-400 border-indigo-505/30' : 'bg-white dark:bg-slate-900/40 text-slate-550 dark:text-slate-400 border-slate-200 dark:border-slate-900 hover:text-slate-900 dark:hover:text-white hover:border-slate-350 dark:hover:border-slate-800' }}">
                <i class="fa-solid {{ $catMeta['icon'] }}"></i>
                <span>{{ $catMeta['label'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Leaderboard Table -->
    <div class="glass border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-805 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-4 sm:px-6 text-center w-20">{{ __('Rank') }}</th>
                        <th class="py-4 px-4 sm:px-6">{{ __('Adventurer') }}</th>
                        <th class="py-4 px-4 sm:px-6 text-right">{{ $metricLabel }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-sm">
                    @if($users->isEmpty())
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-500">{{ __('No activity in this category yet.') }}</td>
                        </tr>
                    @else
                        @foreach($users as $index => $usr)
                            @php $rank = $index + 1; @endphp
                            <tr class="hover:bg-slate-100/30 dark:hover:bg-slate-900/20 transition-colors">
                                <!-- Rank -->
                                <td class="py-4 px-4 sm:px-6 text-center font-black text-slate-800 dark:text-slate-200">
                                    @if($rank === 1)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-400/15 text-amber-500 dark:text-amber-450 border border-amber-400/30 text-base shadow-[0_0_10px_rgba(250,204,21,0.2)]">
                                            🥇
                                        </span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-300/15 text-slate-600 dark:text-slate-300 border border-slate-300/30 text-base">
                                            🥈
                                        </span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-700/15 text-amber-700 border border-amber-700/30 text-base">
                                            🥉
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 text-xs">#{{ $rank }}</span>
                                    @endif
                                </td>

                                <!-- User Name / Avatar -->
                                <td class="py-4 px-4 sm:px-6">
                                    <a href="{{ route('profile.show', $usr) }}" class="inline-flex items-center gap-3 group">
                                        @if($usr->avatar)
                                            <img src="{{ str_starts_with($usr->avatar, 'http') ? $usr->avatar : asset('storage/' . $usr->avatar) }}" alt="{{ $usr->name }}" class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-0">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-600 dark:text-slate-400 text-xs">
                                                {{ strtoupper(substr($usr->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-bold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">{{ $usr->name }}</span>
                                            @if($usr->bio)
                                                <p class="text-[10px] text-slate-500 line-clamp-1 max-w-xs">{{ $usr->bio }}</p>
                                            @endif
                                        </div>
                                    </a>
                                </td>

                                <!-- Metric Value -->
                                <td class="py-4 px-4 sm:px-6 text-right font-bold text-slate-855 dark:text-slate-100">
                                    @if($category === 'points')
                                        <span class="text-amber-600 dark:text-amber-400 flex items-center gap-1 justify-end">
                                            <i class="fa-solid fa-star text-xs"></i> {{ number_format($usr->$metricField) }} {{ __('Pts') }}
                                        </span>
                                    @else
                                        <span>{{ number_format($usr->$metricField) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
