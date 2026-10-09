@extends('layouts.app')

@section('title', __('Item Map'))

@section('styles')
<style>
    #globalMap {
        height: 600px;
        border-radius: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .dark #globalMap {
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .leaflet-popup-content-wrapper {
        background: rgba(255, 255, 255, 0.95);
        color: #1e293b;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .dark .leaflet-popup-content-wrapper {
        background: rgba(15, 23, 42, 0.95);
        color: #f1f5f9;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .leaflet-popup-tip {
        background: rgba(255, 255, 255, 0.95);
    }
    .dark .leaflet-popup-tip {
        background: rgba(15, 23, 42, 0.95);
    }
</style>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Map Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-855 dark:text-white tracking-tight">
                <i class="fa-solid fa-map-location-dot text-indigo-500 dark:text-indigo-400 mr-1.5"></i> {{ __('Distribution') }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">{{ __('Explore the discovery locations of the most unique items across the globe.') }}</p>
        </div>
        
        <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2 self-start shadow-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>{{ __('Total Terpetakan') }}: {{ $items->count() }}</span>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative glass border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-2xl">
        <div id="globalMap" class="shadow-lg"></div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let map;

    // Helper: Haversine distance in km
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function focusOnDensestCluster(items, allMarkers) {
        if (items.length === 0) return;

        let bestMarker = null;
        let maxNeighbors = -1;
        let bestNeighborsList = [];

        items.forEach(item1 => {
            const lat1 = parseFloat(item1.latitude);
            const lng1 = parseFloat(item1.longitude);
            if (isNaN(lat1) || isNaN(lng1)) return;

            const currentNeighbors = [];
            items.forEach(item2 => {
                const lat2 = parseFloat(item2.latitude);
                const lng2 = parseFloat(item2.longitude);
                if (isNaN(lat2) || isNaN(lng2)) return;

                if (getDistance(lat1, lng1, lat2, lng2) <= 1500) {
                    currentNeighbors.push([lat2, lng2]);
                }
            });

            if (currentNeighbors.length > maxNeighbors) {
                maxNeighbors = currentNeighbors.length;
                bestNeighborsList = currentNeighbors;
                bestMarker = [lat1, lng1];
            }
        });

        if (maxNeighbors >= 3) {
            map.fitBounds(bestNeighborsList, { padding: [50, 50], maxZoom: 10 });
        } else {
            map.fitBounds(allMarkers, { padding: [50, 50], maxZoom: 10 });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Default position centered to Indonesia
        const centerLat = -2.5489;
        const centerLng = 118.0149;

        // Initialize Map
        map = L.map('globalMap', {
            zoomControl: true,
            minZoom: 2
        }).setView([centerLat, centerLng], 5);

        updateMapTileLayer();

        // Fetch items from Blade
        const items = @json($items);
        const markersGroup = [];

        items.forEach(item => {
            const lat = parseFloat(item.latitude);
            const lng = parseFloat(item.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            const imageSrc = `{{ asset('storage') }}/${item.image_path}`;
            const detailUrl = `{{ url('/items') }}/${item.id}`;

            const popupContent = `
                <div class="p-2 space-y-2 text-slate-700 dark:text-slate-100 max-w-[200px]">
                    <div class="rounded-lg overflow-hidden aspect-video bg-slate-100 dark:bg-slate-950">
                        <img src="${imageSrc}" alt="${item.title}" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-1">
                        <span class="text-[9px] font-black uppercase tracking-wider text-indigo-655 dark:text-indigo-400">Tier ${item.tier}</span>
                        <h4 class="text-xs font-bold text-slate-855 dark:text-white leading-tight line-clamp-1">
                            <a href="${detailUrl}" class="hover:underline">${item.title}</a>
                        </h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate"><i class="fa-solid fa-location-dot"></i> ${item.location_name || "{{ __('Location Hidden') }}"}</p>
                        <p class="text-[9px] text-slate-455 dark:text-slate-500">{{ __('Found by') }}: <strong>${item.user ? item.user.name : 'Anonim'}</strong></p>
                    </div>
                    <div class="pt-1.5 border-t border-slate-100 dark:border-slate-800">
                        <a href="${detailUrl}" class="block text-center py-1 bg-indigo-650 hover:bg-indigo-505 text-white rounded text-[10px] font-bold transition-colors">{{ __('View Details') }}</a>
                    </div>
                </div>
            `;

            // Plot marker
            L.marker([lat, lng]).addTo(map)
                .bindPopup(popupContent);
            
            markersGroup.push([lat, lng]);
        });

        // Fit bounds based on user location and data density
        if (markersGroup.length > 0) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;

                        const nearbyMarkers = [];
                        items.forEach(item => {
                            const lat = parseFloat(item.latitude);
                            const lng = parseFloat(item.longitude);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                if (getDistance(userLat, userLng, lat, lng) <= 2000) {
                                    nearbyMarkers.push([lat, lng]);
                                }
                            }
                        });

                        // If at least 2 markers exist near the user, focus locally.
                        // Otherwise, fit map to the densest marker cluster.
                        if (nearbyMarkers.length >= 2) {
                            const boundsGroup = [...nearbyMarkers, [userLat, userLng]];
                            map.fitBounds(boundsGroup, { padding: [50, 50], maxZoom: 8 });
                        } else {
                            focusOnDensestCluster(items, markersGroup);
                        }
                    },
                    () => {
                        // Location error/denied: Fall back to cluster focus
                        focusOnDensestCluster(items, markersGroup);
                    },
                    { timeout: 5000 }
                );
            } else {
                // Location not supported: Fall back to cluster focus
                focusOnDensestCluster(items, markersGroup);
            }
        }

        // Re-layer on theme change dynamically
        window.addEventListener('theme-changed', () => {
            updateMapTileLayer();
        });
    });

    function updateMapTileLayer() {
        if (!map) return;
        
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
@endsection
