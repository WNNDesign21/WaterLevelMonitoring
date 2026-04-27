<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Perangkat | Cybernova Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --luxury-primary: #3b82f6;
            --luxury-bg: #f8fafc;
            --luxury-surface: rgba(255, 255, 255, 0.85);
            --luxury-border: rgba(226, 232, 240, 0.8);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--luxury-bg);
            background-image: radial-gradient(circle at top right, rgba(59, 130, 246, 0.05), transparent 40%);
        }
        .glass-panel {
            background: var(--luxury-surface);
            backdrop-filter: blur(16px);
            border: 1px solid var(--luxury-border);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-panel:hover {
            transform: translateY(-5px);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.15);
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
    </style>
</head>
<body class="min-h-screen p-6 md:p-12">

    <div class="max-w-[1200px] mx-auto">
        <!-- Header -->
        <header class="flex flex-col md:flex-row items-center justify-between mb-12">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-blue-600 mb-4 hover:translate-x-1 transition-transform">
                    <i class="fa-solid fa-arrow-left mr-2"></i> KEMBALI KE DASBOR
                </a>
                <h1 class="text-4xl font-bold text-slate-800 tracking-tight">Manajemen <span class="text-blue-600">Perangkat</span></h1>
                <p class="text-slate-500 mt-2">Daftar perangkat sensor yang terintegrasi di jaringan Cybernova.</p>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="glass-panel px-6 py-3 rounded-2xl flex items-center">
                    <div class="text-right mr-4">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Perangkat</div>
                        <div class="text-2xl font-bold text-slate-800">{{ $devices->count() }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Device Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($devices as $device)
            <div class="glass-panel rounded-[2.5rem] overflow-hidden flex flex-col group h-full" data-slug="{{ $device->slug }}">
                <!-- Image Area -->
                <div class="h-48 bg-slate-100 relative overflow-hidden flex items-center justify-center p-6">
                    <img src="{{ asset('images/' . $device->image_path) }}" alt="{{ $device->name }}" class="max-h-full object-contain group-hover:scale-110 transition-transform duration-500">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4 px-3 py-1 rounded-full bg-white/90 backdrop-blur shadow-sm border border-slate-100 flex items-center space-x-2">
                        <div class="status-dot @if($device->status == 'online') bg-emerald-500 animate-pulse @elseif($device->status == 'maintenance') bg-amber-500 @else bg-slate-400 @endif"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-600 status-text">{{ $device->status }}</span>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-8 flex-1 flex flex-col">
                    <div class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-2">{{ $device->type }}</div>
                    <h2 class="text-xl font-bold text-slate-800 mb-2">{{ $device->name }}</h2>
                    <p class="text-sm text-slate-500 mb-6 line-clamp-2">{{ $device->description }}</p>
                    
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center text-xs">
                            <i class="fa-solid fa-location-dot w-5 text-slate-400"></i>
                            <span class="text-slate-600 font-medium">{{ $device->location }}</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <i class="fa-solid fa-barcode w-5 text-slate-400"></i>
                            <span class="text-slate-400 font-mono">{{ $device->serial_number }}</span>
                        </div>
                    </div>

                    <a href="{{ route('devices.show', $device->slug) }}" class="mt-auto w-full py-4 bg-slate-900 text-white rounded-2xl text-center font-bold text-sm hover:bg-blue-600 transition-colors shadow-lg shadow-slate-900/10">
                        LIHAT DETAIL <i class="fa-solid fa-arrow-right ml-2 opacity-50"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Scripts for Real-time -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const echo = new window.Echo({
                broadcaster: 'reverb',
                key: '{{ env('REVERB_APP_KEY', 'local_key') }}',
                wsHost: window.location.hostname,
                wsPort: {{ env('REVERB_PORT', 8080) }},
                wssPort: {{ env('REVERB_PORT', 8080) }},
                forceTLS: (window.location.protocol === 'https:'),
                enabledTransports: ['ws', 'wss'],
                disableStats: true,
            });

            const heartbeats = {}; // Stores timeout IDs for each device

            echo.channel('sensor-data')
                .listen('.sensor.updated', (e) => {
                    // Signal is alive
                    refreshHeartbeat('cybernova-s400-primary');
                })
                .listen('.device.status.updated', (e) => {
                    updateUIStatus(e.device.slug, e.device.status);
                });

            function updateUIStatus(slug, status) {
                const card = document.querySelector(`[data-slug="${slug}"]`);
                if (card) {
                    const statusDot = card.querySelector('.status-dot');
                    const statusText = card.querySelector('.status-text');

                    if (status === 'online') {
                        statusDot.className = 'status-dot bg-emerald-500 animate-pulse';
                        statusText.textContent = 'ONLINE';
                        refreshHeartbeat(slug); // Start/Reset timeout
                    } else {
                        statusDot.className = 'status-dot bg-slate-400';
                        statusText.textContent = status.toUpperCase();
                        if (heartbeats[slug]) clearTimeout(heartbeats[slug]);
                    }
                }
            }

            function refreshHeartbeat(slug) {
                if (heartbeats[slug]) clearTimeout(heartbeats[slug]);
                
                // If it's already offline, mark it online first
                const card = document.querySelector(`[data-slug="${slug}"]`);
                if(card && card.querySelector('.status-text').textContent === 'OFFLINE') {
                    updateUIStatus(slug, 'online');
                }

                heartbeats[slug] = setTimeout(() => {
                    updateUIStatus(slug, 'offline');
                }, 10000); // 10s grace period for heartbeat
            }
        });
    </script>
</body>
</html>
