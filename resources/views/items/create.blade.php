@extends('layouts.app')

@section('title', __('Upload Item'))

@section('styles')
<style>
    #map {
        height: 350px;
        border-radius: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .dark #map {
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    
    <div class="glass border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl relative">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-violet-500/10 rounded-full blur-3xl"></div>

        <div class="mb-8 relative z-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ __('Upload Item') }}</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ __('Upload description') }}</p>
        </div>

        <form action="{{ route('items.store') }}" method="POST" class="space-y-6 relative z-10" id="uploadForm">
            @csrf

            <!-- Base64 Image Input -->
            <input type="hidden" name="image_base64" id="image_base64" required>

            <!-- Camera Capture Section -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Capture Photo') }}</label>
                
                <!-- Live Webcam View -->
                <div id="webcamContainer" class="hidden relative aspect-video w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-200 dark:border-slate-800">
                    <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
                    
                    <!-- Camera HUD overlay -->
                    <div class="absolute inset-0 border-2 border-dashed border-indigo-500/30 rounded-2xl pointer-events-none flex items-center justify-center">
                        <div class="w-24 h-24 border border-indigo-500/40 rounded-full opacity-40"></div>
                    </div>

                    <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-3 px-4">
                        <button type="button" onclick="capturePhoto()"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-505 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-camera"></i> {{ __('Capture Photo') }}
                        </button>
                    </div>
                </div>

                <!-- Preview captured Image -->
                <div id="imagePreviewContainer" class="hidden relative aspect-video w-full rounded-2xl overflow-hidden bg-slate-900 border border-indigo-500/20 shadow-md">
                    <img id="imagePreview" src="#" alt="Hasil Jepretan" class="w-full h-full object-contain">
                    <button type="button" onclick="resetToCamera()"
                        class="absolute top-3 right-3 px-3 py-1.5 bg-slate-955/80 hover:bg-rose-900/60 border border-slate-800 hover:border-rose-505/45 text-slate-350 hover:text-white rounded-lg transition-colors text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i> {{ __('Retake Photo') }}
                    </button>
                </div>

                <!-- Camera Error / Missing Webcam Container -->
                <div id="cameraErrorContainer" class="hidden border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center bg-slate-100/50 dark:bg-indigo-950/5 transition-all">
                    <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/20 rounded-full flex items-center justify-center mx-auto text-rose-500 border border-rose-100 dark:border-rose-900/20">
                        <i class="fa-solid fa-camera-rotate text-lg"></i>
                    </div>
                    <div class="space-y-1 mt-3">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Camera not detected') }}</p>
                        <p class="text-xs text-slate-550">{{ __('Use Simulation Camera') }}</p>
                    </div>
                    <div class="pt-4">
                        <button type="button" onclick="simulateCameraCapture()"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow transition-all cursor-pointer">
                            {{ __('Use Simulation Camera') }}
                        </button>
                    </div>
                </div>

                <!-- Trigger Open Camera Area (Initial State) -->
                <div id="cameraStartTrigger" class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-indigo-500/30 rounded-2xl p-8 text-center cursor-pointer hover:bg-indigo-950/5 transition-all group">
                    <div onclick="startCamera()" class="space-y-3">
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-850 group-hover:text-indigo-650 dark:group-hover:text-indigo-400 transition-colors">
                            <i class="fa-solid fa-video text-xl"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Click to activate camera') }}</p>
                        </div>
                    </div>
                </div>

                @error('image_base64')
                    <p class="text-rose-600 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Title & Description Grid -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">{{ __('Item Name') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="block w-full px-4 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner">
                    @error('title')
                        <p class="text-rose-605 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="4"
                        class="block w-full px-4 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-rose-605 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Location') }}</label>
                    <button type="button" onclick="getCurrentLocation()"
                        class="text-xs text-indigo-605 dark:text-indigo-400 hover:underline font-semibold flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-location-crosshairs"></i> {{ __('Use My Location') }}
                    </button>
                </div>

                <div id="map" class="shadow-lg"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Latitude</label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" readonly
                            class="block w-full px-3 py-2 bg-slate-200/50 dark:bg-slate-950 border border-slate-250 dark:border-slate-900 rounded-lg text-slate-500 dark:text-slate-400 text-xs focus:outline-none cursor-not-allowed shadow-inner"
                            placeholder="Latitude">
                    </div>
                    <div>
                        <label for="longitude" class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Longitude</label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" readonly
                            class="block w-full px-3 py-2 bg-slate-200/50 dark:bg-slate-950 border border-slate-250 dark:border-slate-900 rounded-lg text-slate-500 dark:text-slate-400 text-xs focus:outline-none cursor-not-allowed shadow-inner"
                            placeholder="Longitude">
                    </div>
                </div>

                <div>
                    <label for="location_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">{{ __('Location Name') }}</label>
                    <input type="text" name="location_name" id="location_name" value="{{ old('location_name') }}"
                        class="block w-full px-4 py-3 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-450 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-sm shadow-inner">
                    @error('location_name')
                        <p class="text-rose-650 dark:text-rose-400 text-xs mt-1.5 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="pt-4 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-900">
                <a href="{{ route('items.index') }}" class="px-5 py-2.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 transition-colors shadow-sm">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/40 cursor-pointer">
                    {{ __('Submit') }}
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let map, marker;
    let webcamStream = null;
    
    // Default location (Jakarta, Indonesia)
    const defaultLat = -6.2088;
    const defaultLng = 106.8456;

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Map
        map = L.map('map').setView([defaultLat, defaultLng], 12);
        updateMapTileLayer();

        // Try getting current browser coordinates
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    map.setView([lat, lng], 13);
                    updateMarker(lat, lng);
                },
                () => {}
            );
        }

        // Map Click Event
        map.on('click', (e) => {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            updateMarker(lat, lng);
            reverseGeocode(lat, lng);
        });

        // Re-layer map on theme change dynamically
        window.addEventListener('theme-changed', () => {
            updateMapTileLayer();
        });

        // Check validation on submit
        document.getElementById('uploadForm').addEventListener('submit', (e) => {
            const base64Data = document.getElementById('image_base64').value;
            if (!base64Data) {
                e.preventDefault();
                alert("{{ __('Please capture a photo of the unique item using the camera first!') }}");
            }
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

    function updateMarker(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                updateMarker(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });
        }
    }

    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept-Language': '{{ App::getLocale() }}' }
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                const parts = data.display_name.split(',');
                const displayName = parts.slice(0, 3).join(',').trim();
                document.getElementById('location_name').value = displayName;
            }
        })
        .catch(() => {});
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                map.setView([lat, lng], 15);
                updateMarker(lat, lng);
                reverseGeocode(lat, lng);
            });
        }
    }

    // HTML5 Camera Access
    async function startCamera() {
        document.getElementById('cameraStartTrigger').classList.add('hidden');
        document.getElementById('cameraErrorContainer').classList.add('hidden');
        document.getElementById('webcamContainer').classList.remove('hidden');

        try {
            webcamStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
            });
            const video = document.getElementById('webcam');
            video.srcObject = webcamStream;
        } catch (err) {
            console.error("Camera error:", err);
            document.getElementById('webcamContainer').classList.add('hidden');
            document.getElementById('cameraErrorContainer').classList.remove('hidden');
        }
    }

    function stopCamera() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
    }

    function capturePhoto() {
        const video = document.getElementById('webcam');
        const canvas = document.createElement('canvas');
        
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const base64 = canvas.toDataURL('image/jpeg', 0.9);
        
        document.getElementById('image_base64').value = base64;
        document.getElementById('imagePreview').src = base64;
        
        stopCamera();
        document.getElementById('webcamContainer').classList.add('hidden');
        document.getElementById('imagePreviewContainer').classList.remove('hidden');
    }

    function resetToCamera() {
        document.getElementById('image_base64').value = '';
        document.getElementById('imagePreview').src = '#';
        document.getElementById('imagePreviewContainer').classList.add('hidden');
        startCamera();
    }

    // Camera Simulation (Mock Mode)
    function simulateCameraCapture() {
        const canvas = document.createElement('canvas');
        canvas.width = 400;
        canvas.height = 400;
        const ctx = canvas.getContext('2d');

        const gradients = [
            ['#FFD700', '#FFA500'], // Gold
            ['#8A2BE2', '#4B0082'], // Purple
            ['#FF4500', '#FF0000'], // Crimson
            ['#00FFFF', '#0000FF'], // Blue/Cyan
            ['#00FF00', '#008000']  // Green
        ];
        const colors = gradients[Math.floor(Math.random() * gradients.length)];
        const grad = ctx.createLinearGradient(0, 0, 400, 400);
        grad.addColorStop(0, colors[0]);
        grad.addColorStop(1, colors[1]);
        
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, 400, 400);
        
        ctx.beginPath();
        ctx.arc(200, 200, 75, 0, 2 * Math.PI);
        ctx.fillStyle = '#ffffff';
        ctx.globalAlpha = 0.85;
        ctx.fill();
        ctx.globalAlpha = 1.0;

        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 24px monospace';
        ctx.shadowColor = 'rgba(0,0,0,0.1)';
        ctx.shadowBlur = 4;
        ctx.textAlign = 'center';
        ctx.fillText("UNIQUE OBJECT", 200, 208);

        const base64 = canvas.toDataURL('image/jpeg');
        document.getElementById('image_base64').value = base64;
        document.getElementById('imagePreview').src = base64;

        document.getElementById('cameraErrorContainer').classList.add('hidden');
        document.getElementById('imagePreviewContainer').classList.remove('hidden');
    }
</script>
@endsection
