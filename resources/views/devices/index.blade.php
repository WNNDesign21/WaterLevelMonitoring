@include('partials.dashboard.head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* DIRECT CACHE BYPASS CSS - MASTER ASSEMBLY SYSTEM */
    @keyframes revealFromLeft {
        0% { opacity: 0; transform: translateX(-100px); filter: blur(20px); }
        100% { opacity: 1; transform: translateX(0); filter: blur(0); }
    }
    @keyframes revealFromRight {
        0% { opacity: 0; transform: translateX(100px); filter: blur(20px); }
        100% { opacity: 1; transform: translateX(0); filter: blur(0); }
    }
    @keyframes revealFromBottom {
        0% { opacity: 0; transform: translateY(100px); filter: blur(20px); }
        100% { opacity: 1; transform: translateY(0); filter: blur(0); }
    }
    @keyframes revealFromTop {
        0% { opacity: 0; transform: translateY(-100px); filter: blur(20px); }
        100% { opacity: 1; transform: translateY(0); filter: blur(0); }
    }
    @keyframes revealScale {
        0% { opacity: 0; transform: scale(0.9); filter: blur(20px); }
        100% { opacity: 1; transform: scale(1); filter: blur(0); }
    }

    .v-reveal-left, .v-reveal-right, .v-reveal-bottom, .v-reveal-top, .v-reveal-scale {
        opacity: 0;
        animation-duration: 1.8s;
        animation-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1);
        animation-fill-mode: forwards;
        animation-delay: var(--delay, 0s);
    }

    body.loaded .v-reveal-left { animation-name: revealFromLeft; }
    body.loaded .v-reveal-right { animation-name: revealFromRight; }
    body.loaded .v-reveal-bottom { animation-name: revealFromBottom; }
    body.loaded .v-reveal-top { animation-name: revealFromTop; }
    body.loaded .v-reveal-scale { animation-name: revealScale; }

    /* Precise Staggered Delays - 0.3s Interval for Master Control */
    body.loaded .delay-1 { animation-delay: 0.2s !important; }
    body.loaded .delay-2 { animation-delay: 0.5s !important; }
    body.loaded .delay-3 { animation-delay: 0.8s !important; }
    body.loaded .delay-4 { animation-delay: 1.1s !important; }
    body.loaded .delay-5 { animation-delay: 1.4s !important; }
    body.loaded .delay-6 { animation-delay: 1.7s !important; }
    body.loaded .delay-7 { animation-delay: 2.0s !important; }
    body.loaded .delay-8 { animation-delay: 2.3s !important; }
</style>

<body class="min-h-screen bg-slate-50 text-slate-700 font-sans selection:bg-cyan-100 selection:text-cyan-900 overflow-x-hidden">
    

    
    <!-- Sophisticated Light Background Elements -->
    <div class="fixed inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-400/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- HEADER: Consolidated WaterSense IT Command Center -->
    <div class="max-w-[1800px] mx-auto px-4 md:px-6 pt-6 md:pt-10">
        <div class="glass-panel p-4 md:p-5 rounded-[2rem] bg-white/60 border border-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-6 v-reveal-top" style="--delay: 0.2s">
            <!-- Branding Section (Left) -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('it.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-slate-900 text-white rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-slate-900/20 group">
                    <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                </a>
                <div class="flex items-center space-x-3 border-r border-slate-200 pr-6 mr-2 hidden md:flex">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                        <i class="fa-solid fa-microchip text-white text-base"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-black text-slate-800 tracking-tighter uppercase leading-none">Device<span class="text-cyan-600">Manager</span></h1>
                        <p class="text-[8px] font-mono text-cyan-600 tracking-[0.3em] uppercase mt-1">INFRASTRUCTURE_CTRL</p>
                    </div>
                </div>
                <!-- Real-time Clock -->
                <div class="hidden xl:flex flex-col">
                    <span id="header-time" class="text-xs font-black text-slate-700 font-mono tracking-widest">00:00:00</span>
                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-1">WIB_KARAWANG_SECTOR</span>
                </div>
            </div>

            <!-- Central Status & Actions -->
            <div class="flex flex-wrap items-center justify-center gap-4">
                <button onclick="openDeviceModal('add')" class="flex items-center space-x-2 bg-slate-900 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-cyan-600 transition-all shadow-lg shadow-slate-900/20">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Register Node</span>
                </button>

                <div class="flex items-center bg-slate-100/50 p-1 rounded-2xl border border-slate-200/30">
                    <a href="{{ route('it.dashboard') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all">Dashboard</a>
                    <div class="w-px h-4 bg-slate-200 mx-1"></div>
                    <a href="{{ route('it.analytics.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-blue-600 hover:text-white transition-all">Analytics</a>
                    <div class="w-px h-4 bg-slate-200 mx-1"></div>
                    <a href="{{ route('it.users.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-cyan-500 hover:text-white transition-all">Users</a>
                </div>
            </div>

            <!-- Profile Section (Right) -->
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <div class="text-[11px] font-black text-slate-800 uppercase tracking-tighter leading-none">{{ auth()->user()->name }}</div>
                    <div class="text-[8px] font-bold text-cyan-600 uppercase tracking-widest mt-1.5 flex items-center justify-end">IT_ADMIN_RANK</div>
                </div>
                <div class="flex items-center space-x-2">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0f172a&color=fff' }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-lg">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all shadow-lg flex items-center justify-center group">
                            <i class="fa-solid fa-power-off text-xs group-hover:scale-110 transition-transform"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateHeaderClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const element = document.getElementById('header-time');
            if(element) element.textContent = timeStr.replace(/\./g, ':');
        }
        setInterval(updateHeaderClock, 1000);
        updateHeaderClock();
    </script>

    <main class="relative z-10 max-w-[1800px] mx-auto px-4 md:px-6 py-6 md:py-10">
        
        <!-- Header Hero Section (Light) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 mb-8 md:mb-12">
            <div class="lg:col-span-8 v-reveal-left delay-1 text-center md:text-left">
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tighter mb-4 leading-tight">Device Node <br class="md:hidden"><span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-700 font-black">Infrastructure</span></h2>
                <p class="text-slate-500 text-sm md:text-lg max-w-2xl leading-relaxed font-medium">Kelola seluruh armada sensor telemetri Anda dengan presisi tingkat militer. Pantau status konektivitas, kalibrasi sensor, dan konfigurasi GPS secara terpusat.</p>
            </div>
            <div class="lg:col-span-4 grid grid-cols-2 gap-4">
                <div class="v-reveal-bottom delay-2 bg-white border border-slate-200 rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 flex flex-col justify-center shadow-sm">
                    <span class="text-[8px] md:text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-1 md:mb-2">Total Nodes</span>
                    <div class="text-2xl md:text-4xl font-black text-slate-800 font-mono tracking-tighter">{{ count($devices) }}</div>
                </div>
                <div class="v-reveal-bottom delay-2 bg-white border border-slate-200 rounded-[1.5rem] md:rounded-[2rem] p-4 md:p-6 flex flex-col justify-center shadow-sm">
                    <span class="text-[8px] md:text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1 md:mb-2">Active Signal</span>
                    <div class="text-2xl md:text-4xl font-black text-slate-800 font-mono tracking-tighter">{{ $devices->where('status', 'online')->count() }}</div>
                </div>
            </div>
        </div>
        
        <!-- GIS ASSET LOCATOR (NEW) -->
        <div class="mb-10 relative z-20">
            <div class="glass-panel p-2 rounded-[2.5rem] bg-slate-100 border border-white shadow-2xl overflow-hidden relative v-reveal-scale delay-2" style="height: 400px; will-change: transform, opacity;">
                <div id="master-map" class="w-full h-full rounded-[2.2rem] z-10 opacity-0 transition-opacity duration-1000 bg-slate-200"></div>
                
                <!-- Map Legend -->
                <div class="absolute bottom-6 left-6 z-[1000] bg-white/95 backdrop-blur-md border border-slate-200 p-4 rounded-2xl shadow-xl">
                    <h4 class="text-[9px] font-black text-slate-800 uppercase tracking-widest border-b border-slate-100 pb-2 mb-3">Asset Status</h4>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] font-bold text-slate-600 uppercase">Operational</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-[9px] font-bold text-slate-600 uppercase">Faulty / Offline</span>
                        </div>
                    </div>
                </div>

                <!-- Floating Title -->
                <div class="absolute top-6 right-6 z-[1000] pointer-events-none">
                    <div class="bg-slate-900/90 backdrop-blur-md px-5 py-2.5 rounded-xl border border-slate-700 shadow-2xl text-right">
                        <h3 class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Geospatial_Asset_Intelligence</h3>
                        <p class="text-[8px] font-bold text-cyan-400 uppercase tracking-widest mt-1">SENTINEL_LOCATOR_SYSTEM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Flash Messages -->
        @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center text-emerald-600 animate-slide-up shadow-sm">
            <i class="fa-solid fa-circle-check text-xl mr-3"></i>
            <span class="text-sm font-bold uppercase tracking-widest">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Advanced Device Matrix (Light Mode) -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            @foreach($devices as $index => $device)
            <div class="v-reveal-bottom delay-{{ ($index % 5) + 4 }} group relative bg-white hover:bg-slate-50 border border-slate-200 hover:border-cyan-500/50 rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-8 transition-all duration-500 shadow-md hover:shadow-2xl hover:shadow-cyan-500/10 overflow-hidden">
                
                <!-- Status Badge -->
                <div class="absolute top-6 md:top-8 right-6 md:right-8 flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : ($device->status === 'maintenance' ? 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' : 'bg-slate-300') }} animate-pulse"></span>
                    <span class="text-[8px] md:text-[9px] font-black uppercase tracking-widest text-slate-600">{{ $device->status }}</span>
                </div>

                <div class="flex items-start justify-between mb-6 md:mb-8">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-[1.5rem] bg-gradient-to-br {{ $device->status === 'online' ? 'from-cyan-500 to-blue-600 shadow-cyan-500/20' : 'from-slate-400 to-slate-500 shadow-slate-300/20' }} flex items-center justify-center shadow-xl">
                        <i class="fa-solid {{ $device->type === 'Ultrasonic Sensor' ? 'fa-wave-square' : 'fa-microchip' }} text-white text-xl md:text-2xl"></i>
                    </div>
                </div>

                <div class="space-y-1 mb-6">
                    <h3 class="text-xl md:text-2xl font-black text-slate-800 group-hover:text-cyan-600 transition-colors tracking-tight">{{ $device->name }}</h3>
                    <div class="flex items-center space-x-2 text-[10px] md:text-xs font-mono text-slate-400">
                        <i class="fa-solid fa-barcode text-[10px]"></i>
                        <span>SN: {{ $device->serial_number }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 md:gap-4 mb-6 md:mb-8">
                    <div class="bg-slate-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-slate-100">
                        <span class="text-[7px] md:text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Latitude</span>
                        <span class="text-[10px] md:text-xs font-mono text-slate-700 font-bold truncate block">{{ $device->latitude ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-slate-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-slate-100">
                        <span class="text-[7px] md:text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Longitude</span>
                        <span class="text-[10px] md:text-xs font-mono text-slate-700 font-bold truncate block">{{ $device->longitude ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between pt-4 md:pt-6 border-t border-slate-100 gap-3">
                    <div class="flex space-x-2">
                        <button onclick="openCalibrationModal({{ json_encode($device) }})" class="w-9 h-9 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-slate-50 hover:bg-cyan-500 text-slate-400 hover:text-white transition-all flex items-center justify-center border border-slate-200 shadow-sm" title="Calibrate">
                            <i class="fa-solid fa-sliders text-xs md:text-sm"></i>
                        </button>
                        <button onclick="openDeviceModal('edit', {{ json_encode($device) }})" class="w-9 h-9 md:w-10 md:h-10 rounded-lg md:rounded-xl bg-slate-50 hover:bg-blue-600 text-slate-400 hover:text-white transition-all flex items-center justify-center border border-slate-200 shadow-sm" title="Edit">
                            <i class="fa-solid fa-pen-to-square text-xs md:text-sm"></i>
                        </button>
                    </div>
                    
                    <form action="{{ route('it.devices.destroy', $device->id) }}" method="POST" onsubmit="return confirm('DESTROY NODE: Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.');" class="flex-1 md:flex-none">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="h-9 md:h-10 w-full md:w-auto px-4 rounded-lg md:rounded-xl bg-red-50 hover:bg-red-500 text-red-500 hover:text-white transition-all border border-red-100 text-[8px] md:text-[10px] font-black uppercase tracking-widest flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-trash-can mr-2"></i> Terminate
                        </button>
                    </form>
                </div>

                <!-- Hover Decorative Element -->
                <div class="absolute bottom-4 right-8 text-[8px] font-mono text-slate-200 uppercase tracking-[0.4em] select-none group-hover:text-cyan-500/10 transition-colors font-black">
                    Hardware ID: {{ substr($device->slug, -8) }}
                </div>
            </div>
            @endforeach

            <!-- Add New Card (Light) -->
            <button onclick="openDeviceModal('add')" class="group relative bg-white hover:bg-slate-50 border-2 border-dashed border-slate-200 hover:border-cyan-500 rounded-[2.5rem] p-8 transition-all duration-500 flex flex-col items-center justify-center space-y-4 min-h-[350px] shadow-sm">
                <div class="w-20 h-20 rounded-full bg-slate-50 group-hover:bg-cyan-500 group-hover:rotate-90 transition-all duration-500 flex items-center justify-center border border-slate-100 shadow-inner">
                    <i class="fa-solid fa-plus text-3xl text-slate-300 group-hover:text-white"></i>
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-widest">Register New Node</h3>
                    <p class="text-slate-400 text-xs mt-2 font-bold tracking-widest uppercase">Expand Infrastructure</p>
                </div>
            </button>
        </div>

    </main>

    <!-- DEVICE MODAL (Add/Edit) - Light Mode Upgrade -->
    <div id="deviceModal" class="fixed inset-0 z-[1000] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_70px_rgba(0,0,0,0.15)] w-full max-w-2xl mx-4 overflow-hidden border border-slate-100 transform transition-all scale-95 opacity-0 duration-300" id="deviceModalContent">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.3em]" id="modalTitle">Register Node</h3>
                    <p class="text-[9px] font-mono text-cyan-600 uppercase mt-1 font-bold">Direct Hardware Configuration</p>
                </div>
                <button onclick="closeDeviceModal()" class="w-10 h-10 rounded-full bg-white hover:bg-red-500 text-slate-400 hover:text-white transition-all flex items-center justify-center shadow-sm border border-slate-100"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="deviceForm" method="POST" action="{{ route('it.devices.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Node Designation</label>
                        <input type="text" name="name" id="dev_name" required placeholder="e.g. Karawang Barat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sensor Class</label>
                        <input type="text" name="type" id="dev_type" required value="Ultrasonic Sensor" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Serial Authentication</label>
                        <input type="text" name="serial_number" id="dev_sn" required placeholder="NMCU-XXXX-XXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deployment Sector</label>
                        <input type="text" name="location" id="dev_loc" placeholder="Karawang, Jawa Barat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Geo Latitude</label>
                        <input type="number" step="any" name="latitude" id="dev_lat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Geo Longitude</label>
                        <input type="number" step="any" name="longitude" id="dev_lng" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-2 md:col-span-2 hidden" id="statusGroup">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Operational Status</label>
                        <select name="status" id="dev_status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                            <option value="online">ONLINE</option>
                            <option value="offline">OFFLINE</option>
                            <option value="maintenance">MAINTENANCE</option>
                        </select>
                    </div>
                </div>
                
                <div class="px-8 py-6 border-t border-slate-100 flex justify-end space-x-4 bg-slate-50">
                    <button type="button" onclick="closeDeviceModal()" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-colors font-bold">Abort</button>
                    <button type="submit" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest bg-slate-800 text-white shadow-xl shadow-slate-200 hover:scale-105 active:scale-95 transition-all flex items-center">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Deploy Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('partials.dashboard.calibration_modal')
    @include('partials.dashboard.scripts')

    <script>
        // Trigger Animations with 'Human-Eye' Sync Delay
        function triggerReveal() {
            if(!document.body.classList.contains('loaded')) {
                console.log('[SENTINEL-SYSTEM] Triggering Device HQ Reveal...');
                document.body.classList.add('loaded'); 
            }
        }

        document.addEventListener('DOMContentLoaded', triggerReveal);
        window.addEventListener('load', triggerReveal);
        setTimeout(triggerReveal, 3000); // EMERGENCY FAIL-SAFE (3 Seconds)

        function openDeviceModal(mode, device = null) {
            const modal = document.getElementById('deviceModal');
            const content = document.getElementById('deviceModalContent');
            const form = document.getElementById('deviceForm');
            const title = document.getElementById('modalTitle');
            const method = document.getElementById('formMethod');
            const statusGroup = document.getElementById('statusGroup');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);

            if (mode === 'add') {
                title.innerText = 'Register New Node';
                form.action = "{{ route('it.devices.store') }}";
                method.value = 'POST';
                form.reset();
                statusGroup.classList.add('hidden');
            } else if (mode === 'edit') {
                title.innerText = 'Update Node Configuration';
                form.action = "/it/devices/" + device.id;
                method.value = 'PUT';
                statusGroup.classList.remove('hidden');
                
                document.getElementById('dev_name').value = device.name || '';
                document.getElementById('dev_type').value = device.type || '';
                document.getElementById('dev_sn').value = device.serial_number || '';
                document.getElementById('dev_loc').value = device.location || '';
                document.getElementById('dev_lat').value = device.latitude || '';
                document.getElementById('dev_lng').value = device.longitude || '';
                if(document.getElementById('dev_status')) {
                    document.getElementById('dev_status').value = device.status || 'offline';
                }
            }
        }

        function closeDeviceModal() {
            const modal = document.getElementById('deviceModal');
            const content = document.getElementById('deviceModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        // --- GIS MAP LOGIC (LEAFLET) ---
        let map = null;
        let markers = {};

        function initMasterMap() {
            // Start with a "Global View" (Zoom level 2)
            map = L.map('master-map', {
                zoomControl: false,
                attributionControl: false,
                scrollWheelZoom: false // Disable during animation
            }).setView([-2.5489, 118.0149], 2); // Center of Indonesia but zoomed way out

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            L.control.zoom({ position: 'topright' }).addTo(map);
            
            // Add initial markers
            @foreach($devices as $device)
                @if($device->latitude && $device->longitude)
                    addMapMarker(
                        {{ $device->latitude }}, 
                        {{ $device->longitude }}, 
                        '{{ addslashes($device->name) }}', 
                        '{{ $device->status }}',
                        '{{ $device->slug }}'
                    );
                @endif
            @endforeach
        }

        // ... (addMapMarker function remains same) ...
        function addMapMarker(lat, lng, name, status, slug) {
            const color = status === 'online' ? '#10b981' : '#ef4444';
            const iconHtml = `
                <div class="relative group">
                    <div class="w-5 h-5 rounded-full" style="background-color: ${color}; border: 3px solid white; box-shadow: 0 0 15px rgba(0,0,0,0.3);"></div>
                    ${status === 'online' ? `<div class="absolute inset-0 w-5 h-5 rounded-full animate-ping" style="background-color: ${color}; opacity: 0.4;"></div>` : ''}
                </div>
            `;

            const customIcon = L.divIcon({
                html: iconHtml,
                className: 'custom-div-icon',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
            
            const popupContent = `
                <div class="p-3 min-w-[160px]">
                    <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Asset_Node</div>
                    <h4 class="text-[11px] font-black text-slate-800 uppercase mb-2 border-b border-slate-100 pb-1">${name}</h4>
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="w-2 h-2 rounded-full ${status === 'online' ? 'bg-emerald-500' : 'bg-red-500'}"></span>
                        <span class="text-[9px] font-black uppercase text-slate-500 tracking-tighter">${status.toUpperCase()}</span>
                    </div>
                    <div class="text-[7px] font-mono text-slate-400 bg-slate-50 p-1.5 rounded border border-slate-100">
                        LAT: ${lat.toFixed(4)}<br>LNG: ${lng.toFixed(4)}
                    </div>
                </div>
            `;
            marker.bindPopup(popupContent, {
                className: 'custom-leaflet-popup',
                closeButton: false
            });
            
            markers[slug] = marker;
        }

        // Initialize on load with Fly-In Sequence
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                initMasterMap();
                const mapEl = document.getElementById('master-map');
                if(mapEl) mapEl.style.opacity = '1';
                
                // --- CINEMATIC FLY-IN SEQUENCE ---
                setTimeout(() => {
                    if(map) {
                        map.invalidateSize();
                        // Flying to Karawang Sector from Space
                        map.flyTo([-6.3227, 107.3376], 12, {
                            animate: true,
                            duration: 4, // 4 seconds of smooth flight
                            easeLinearity: 0.25
                        });
                        
                        // Re-enable scroll zoom after landing
                        setTimeout(() => {
                            map.scrollWheelZoom.enable();
                        }, 4000);
                    }
                }, 800); // Wait for container reveal animation to be almost done
            }, 500);
        });
    </script>
</body>
</html>
