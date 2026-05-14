@include('partials.dashboard.head')

<style>
    /* DIRECT CACHE BYPASS CSS - NOC ASSEMBLY SYSTEM */
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

    .v-reveal-left, .v-reveal-right, .v-reveal-bottom {
        opacity: 0;
        animation-duration: 1.5s;
        animation-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1);
        animation-fill-mode: forwards;
    }

    .v-reveal-header { 
        opacity: 0; 
        transform: translateY(-70px);
        filter: blur(15px);
        transition: all 1.4s cubic-bezier(0.19, 1, 0.22, 1);
    }
    body.loaded .v-reveal-header { 
        opacity: 1; 
        transform: translateY(0);
        filter: blur(0);
    }

    .v-reveal-item { opacity: 0; }
    body.loaded .v-reveal-item { animation: revealFadeUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

    body.loaded .v-reveal-left { animation-name: revealFromLeft; }
    body.loaded .v-reveal-right { animation-name: revealFromRight; }
    body.loaded .v-reveal-bottom { animation-name: revealFromBottom; }

    body.loaded .delay-1 { animation-delay: 0.2s !important; }
    body.loaded .delay-2 { animation-delay: 0.6s !important; }
    body.loaded .delay-3 { animation-delay: 1.0s !important; }
    body.loaded .delay-4 { animation-delay: 1.4s !important; }
    body.loaded .delay-5 { animation-delay: 1.8s !important; }
    body.loaded .delay-6 { animation-delay: 2.2s !important; }
    body.loaded .delay-7 { animation-delay: 2.6s !important; }
    body.loaded .delay-8 { animation-delay: 3.0s !important; }
    body.loaded .delay-9 { animation-delay: 3.4s !important; }
</style>

<body class="min-h-screen bg-slate-50 text-slate-700 font-sans selection:bg-cyan-200 selection:text-cyan-900 pb-10 overflow-x-hidden">
    

    
    <!-- Top Accent Line -->
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-500 z-50 shadow-[0_0_10px_rgba(6,182,212,0.3)]"></div>

    <!-- Main Container -->
    <div class="w-full max-w-[2000px] mx-auto px-4 sm:px-6 lg:px-8 mt-6 relative z-10">
        
        <!-- HEADER: Consolidated WaterSense IT Command Center -->
        <div class="mb-8 v-reveal-header">
            <div class="glass-panel p-4 md:p-6 rounded-[2rem] bg-white/60 border border-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-6">
                <!-- Branding Section (Left) -->
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0">
                        <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" class="w-12 h-12 object-contain drop-shadow-lg">
                    </div>
                    <div class="border-r border-slate-200 pr-6 mr-2 hidden md:block">
                        <h1 class="text-xl font-black text-slate-800 tracking-tighter uppercase leading-none">Water<span class="text-cyan-600">Sense</span></h1>
                        <p class="text-[9px] font-mono text-cyan-600 tracking-[0.3em] uppercase mt-1">IT_COMMAND_CENTER</p>
                    </div>
                    <!-- Real-time Clock (Middle Bridge) -->
                    <div class="hidden xl:flex flex-col">
                        <span id="header-time" class="text-xs font-black text-slate-700 font-mono tracking-widest">00:00:00</span>
                        <span id="header-date" class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-1">WIB_KARAWANG_SECTOR</span>
                    </div>
                </div>

                <!-- Central Status & Actions -->
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <!-- Global Connection Badge -->
                    <div id="global-connectivity-badge" class="flex items-center space-x-3 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl shadow-sm transition-all duration-500">
                        <div class="relative">
                            <span id="global-status-dot" class="w-2.5 h-2.5 rounded-full bg-emerald-500 block"></span>
                            <span id="global-status-ping" class="absolute inset-0 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping opacity-75"></span>
                        </div>
                        <span id="global-status-text" class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">SENTINEL_ONLINE</span>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center bg-slate-100/50 p-1 rounded-2xl border border-slate-200/30">
                        <a href="{{ route('it.users.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all flex items-center">
                            <i class="fa-solid fa-users-gear mr-2 text-[12px]"></i> Users
                        </a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.analytics.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-blue-600 hover:text-white transition-all flex items-center">
                            <i class="fa-solid fa-chart-line mr-2 text-[12px]"></i> Analytics
                        </a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.devices.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-cyan-500 hover:text-white transition-all flex items-center">
                            <i class="fa-solid fa-gears mr-2 text-[12px]"></i> Device
                        </a>
                    </div>

                    <!-- View Live Portal -->
                    <a href="{{ route('user.dashboard') }}" target="_blank" class="flex items-center space-x-3 bg-gradient-to-r from-slate-800 to-slate-900 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:scale-105 active:scale-95 transition-all shadow-xl shadow-slate-200 group border border-slate-700">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                        </span>
                        <span>Lihat Live</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-500 group-hover:text-white transition-colors"></i>
                    </a>
                </div>

                <!-- Profile Section (Right) -->
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-[11px] font-black text-slate-800 uppercase tracking-tighter leading-none">{{ auth()->user()->name }}</div>
                        <div class="text-[8px] font-bold text-cyan-600 uppercase tracking-widest mt-1.5 flex items-center justify-end">
                            <span class="w-1 h-1 rounded-full bg-cyan-500 mr-1.5"></span> IT_ADMIN_RANK
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0f172a&color=fff' }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-lg">
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-10 h-10 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 flex items-center justify-center group">
                                <i class="fa-solid fa-power-off text-[12px] group-hover:scale-110 transition-transform"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Real-time Header Clock
            function updateHeaderClock() {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const element = document.getElementById('header-time');
                if(element) element.textContent = timeStr.replace(/\./g, ':');
            }
            setInterval(updateHeaderClock, 1000);
            updateHeaderClock();
        </script>

        <!-- TOP BAR: Micro Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <div class="v-reveal-bottom delay-1 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-clock text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Waktu Aktif Server</span>
                <span class="text-sm font-mono text-emerald-500" id="it-uptime">99.98%</span>
            </div>
            <div class="v-reveal-bottom delay-2 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-network-wired text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Latensi Reverb</span>
                <span class="text-sm font-mono text-cyan-600"><span id="it-ping">12</span> ms</span>
            </div>
            <div class="v-reveal-bottom delay-3 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-bolt text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tegangan Node</span>
                <span class="text-sm font-mono text-amber-500" id="it-voltage">5.02 V</span>
            </div>
            <div class="v-reveal-bottom delay-4 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-users text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Koneksi Aktif</span>
                <span class="text-sm font-mono text-blue-500" id="it-connections">1,204</span>
            </div>
            <div class="v-reveal-bottom delay-5 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-memory text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Penggunaan Memori</span>
                <span class="text-sm font-mono text-slate-600" id="it-memory">42.8 GB / 64.0 GB</span>
            </div>
            <div class="v-reveal-bottom delay-6 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-database text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Kueri DB/Detik</span>
                <span class="text-sm font-mono text-slate-600" id="it-qps">450</span>
            </div>
        </div>

        <!-- DENSE GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            
            <!-- LEFT COLUMN (3 Cols) -->
            <div class="lg:col-span-3 flex flex-col space-y-5">
                
                <!-- System Diagnostics -->
                <div class="v-reveal-left delay-7 bg-white border border-slate-100 rounded-[1.5rem] p-5 relative overflow-hidden shadow-lg">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-2xl"></div>
                    <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-5 flex items-center border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-microchip mr-2 text-cyan-500"></i> Status Perangkat Keras
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between text-[10px] mb-1.5 font-mono">
                                <span class="text-slate-500">SUHU_CPU</span>
                                <span class="text-amber-500 font-bold" id="it-cpu-temp">48.5°C</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div id="it-cpu-bar" class="bg-gradient-to-r from-amber-400 to-amber-300 h-1.5 rounded-full transition-all duration-500" style="width: 48%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[10px] mb-1.5 font-mono">
                                <span class="text-slate-500">RUGI_PAKET</span>
                                <span class="text-emerald-500 font-bold" id="it-packet-loss">0.00%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div id="it-packet-bar" class="bg-gradient-to-r from-emerald-400 to-emerald-300 h-1.5 rounded-full transition-all duration-500" style="width: 100%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[10px] mb-1.5 font-mono">
                                <span class="text-slate-500">KUAT_SINYAL</span>
                                <span class="text-cyan-500 font-bold" id="it-signal-strength">-64 dBm</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div id="it-signal-bar" class="bg-gradient-to-r from-cyan-500 to-cyan-400 h-1.5 rounded-full transition-all duration-500" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terminal Log -->
                <div class="v-reveal-left delay-8 bg-slate-100 border border-slate-200 rounded-[1.5rem] p-4 flex-1 flex flex-col h-[300px] shadow-inner relative">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-3 shrink-0">
                        <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center">
                            <i class="fa-solid fa-terminal mr-2 text-slate-400"></i> Log Sistem
                        </h3>
                        <span class="text-[8px] font-mono text-emerald-600 bg-emerald-500/10 px-1.5 py-0.5 rounded animate-pulse">MEMANTAU...</span>
                    </div>
                    <div class="relative flex-1">
                        <div id="it-terminal" class="absolute inset-0 font-mono text-[9px] leading-relaxed text-slate-500 overflow-y-auto pr-2" style="overflow-anchor: none;">
                            <div class="space-y-1.5 min-h-full flex flex-col justify-end">
                                <div class="text-slate-500">[SYS] Memulai proses jabat tangan...</div>
                                <div class="text-emerald-600">[OK] Saluran aman berhasil dibuat.</div>
                                <div class="text-cyan-600">[ALIRAN] Menunggu telemetri masuk.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CENTER COLUMN (6 Cols) -->
            <div class="lg:col-span-6 flex flex-col space-y-5">
                
                <!-- Raw Telemetry KPI -->
                <div class="v-reveal-right delay-9 bg-white border border-slate-100 rounded-[2rem] p-6 relative overflow-hidden shadow-xl flex flex-col">
                    <div class="absolute -right-10 -bottom-10 opacity-5 pointer-events-none">
                        <i class="fa-solid fa-water text-9xl"></i>
                    </div>

                    <!-- Universal Node Selector (Proportional Layout) -->
                    <div class="mb-10 border-b border-slate-50 pb-6 flex flex-col md:flex-row items-center justify-between gap-6 bg-slate-50/40 p-4 rounded-2xl border border-white/50 shadow-sm">
                        <div class="flex items-center space-x-4">
                            <div class="w-1.5 h-10 bg-gradient-to-b from-cyan-500 to-blue-600 rounded-full shadow-[0_0_10px_rgba(6,182,212,0.3)]"></div>
                            <div>
                                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] leading-none mb-1.5">Pilih Device</h2>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">SENTINEL_SOURCE_UPLINK</p>
                            </div>
                        </div>

                        <div class="relative group/selector w-full md:w-96" id="device-selector-wrapper">
                            <button type="button" onclick="toggleDeviceSelector()" id="device-selector-btn" class="w-full bg-white hover:bg-slate-50 border border-slate-200 px-5 py-3 rounded-xl transition-all shadow-sm flex items-center justify-between group">
                                <div class="flex items-center space-x-4">
                                    <div class="w-7 h-7 rounded-lg bg-slate-900 flex items-center justify-center shrink-0 shadow-md group-hover:bg-cyan-600 transition-colors">
                                        <i class="fa-solid fa-satellite-dish text-white text-[10px]"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">ACTIVE_NODE</div>
                                        <div class="text-[10px] font-black text-slate-700 uppercase tracking-tight truncate max-w-[150px] md:max-w-xs" id="active-device-name">{{ $primaryDevice->name ?? 'PILIH_SENSOR' }}</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 ml-4 transition-transform duration-300" id="device-selector-icon"></i>
                            </button>
                            
                            <div id="device-selector-menu" class="absolute top-full left-0 right-0 mt-2 bg-white/95 backdrop-blur-2xl border border-slate-200 shadow-[0_30px_60px_rgba(0,0,0,0.15)] rounded-2xl overflow-hidden transition-all duration-300 origin-top transform scale-y-0 opacity-0 invisible max-h-[300px] overflow-y-auto z-[999]">
                                <div class="p-2 space-y-1">
                                    @foreach($allDevices ?? [] as $device)
                                    <button type="button" onclick="userSwitchDevice('{{ $device->slug }}', '{{ addslashes($device->name) }}', '{{ addslashes($device->location) }}', {{ $device->latitude ?? -6.2088 }}, {{ $device->longitude ?? 106.8456 }})" class="w-full text-left p-3.5 rounded-xl hover:bg-slate-50 transition-colors flex items-center space-x-4 group/item border border-transparent hover:border-slate-100">
                                        <div id="status-dot-{{ $device->slug }}" class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : 'bg-red-500' }} shrink-0"></div>
                                        <div class="flex-1 overflow-hidden">
                                            <div class="text-[11px] font-black text-slate-700 truncate tracking-tight">{{ $device->name }}</div>
                                            <div class="text-[7px] text-slate-400 font-mono uppercase tracking-widest mt-0.5">{{ substr($device->slug, -10) }}</div>
                                        </div>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-6 relative z-10">
                        <div class="col-span-3 lg:col-span-1 border-b lg:border-b-0 lg:border-r border-slate-100 pb-4 lg:pb-0 relative">
                            <h3 class="text-[9px] text-slate-400 font-black uppercase tracking-[0.2em] mb-2">Jarak Sensor</h3>
                            <div class="flex items-baseline">
                                <span id="current-distance" class="text-4xl font-black text-slate-700 font-mono odometer">--</span>
                                <span class="text-sm text-slate-400 ml-2 font-mono">cm</span>
                            </div>
                        </div>
                        <div class="col-span-3 lg:col-span-1 border-b lg:border-b-0 lg:border-r border-slate-100 pb-4 lg:pb-0">
                            <h3 class="text-[9px] text-slate-400 font-black mb-2 uppercase tracking-[0.2em]">Kecepatan Aliran</h3>
                            <div class="flex items-baseline">
                                <span id="flow-velocity" class="text-4xl font-black text-cyan-500 font-mono odometer">0.00</span>
                                <span class="text-sm text-slate-400 ml-2 font-mono">cm/s</span>
                            </div>
                        </div>
                        <div class="col-span-3 lg:col-span-1">
                            <h3 class="text-[9px] text-slate-400 font-black mb-2 uppercase tracking-[0.2em]">Tinggi Permukaan Air</h3>
                            <div class="flex items-baseline">
                                <span id="water-level" class="text-5xl font-black text-slate-800 font-mono odometer skeleton w-32 h-14"></span>
                                <span class="text-sm text-blue-500 ml-2 font-bold font-mono">MDPL</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sparkline inside KPI -->
                    <div class="mt-6 h-20 w-full relative z-10">
                        <canvas id="tma-sparkline"></canvas>
                    </div>
                </div>

                <!-- Wireframe River Tank -->
                <div class="v-reveal-left delay-7 bg-white border border-slate-100 rounded-[2rem] p-3 flex-1 relative min-h-[350px] shadow-inner group">
                    <div class="absolute top-5 left-5 z-[100] bg-white border border-slate-200 px-3 py-1.5 rounded-lg flex items-center space-x-2 shadow-sm">
                        <i class="fa-solid fa-layer-group text-slate-400 text-[10px]"></i>
                        <span class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Simulasi Visual</span>
                    </div>
                    
                    <!-- X-Ray Glass Tank -->
                    <div class="glass-tank-container relative w-full h-full rounded-[1.5rem] bg-slate-50 border border-slate-200 overflow-hidden shadow-[inset_0_0_30px_rgba(0,0,0,0.05)]">
                        
                        <!-- Water Layer -->
                        <div class="liquid-water transition-all duration-700 ease-out" id="river-water" style="height: 0%;">
                            <div class="liquid-water-particles"></div>
                            <!-- Dynamic Percent Label -->
                            <div id="water-percent-container" class="absolute inset-x-0 top-1/2 -translate-y-1/2 text-center transition-all duration-700 pointer-events-none z-20">
                                <div id="water-percent" class="text-6xl font-black text-white/20 uppercase tracking-tighter mix-blend-overlay font-mono">0%</div>
                            </div>
                        </div>

                        <!-- Foreground Overlays (High Contrast) -->
                        <div class="absolute inset-0 z-40 pointer-events-none">
                            @for($i = 0; $i <= 600; $i += 50)
                                @php 
                                    $bottomPercent = ($i / 600) * 100; 
                                    $tmaValue = 8.00 + ($i / 100);
                                @endphp
                                @if($i % 100 == 0)
                                    <div class="absolute right-0 left-0 h-px bg-slate-900/[0.08]" style="bottom: {{ $bottomPercent }}%"></div>
                                    <div class="depth-tick !w-12 !bg-slate-900" style="bottom: {{ $bottomPercent }}%"></div>
                                    <div class="depth-value !text-[10px]" style="bottom: {{ $bottomPercent }}%; transform: translateY(50%)">{{ number_format($tmaValue, 2) }}</div>
                                @else
                                    <div class="depth-tick !w-4" style="bottom: {{ $bottomPercent }}%"></div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (3 Cols) -->
            <div class="lg:col-span-3 flex flex-col space-y-5">
                
                <!-- Weather Metric (New for NOC) -->
                <div class="v-reveal-bottom delay-2 bg-white border border-slate-100 rounded-[1.5rem] p-4 relative overflow-hidden shadow-lg mb-5 group">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center">
                            <i class="fa-solid fa-cloud-sun mr-2 text-blue-500"></i> Kondisi Cuaca Satelit
                        </h3>
                        <span id="sky-location-detail" class="text-[8px] font-mono text-slate-400 uppercase">GPS_SIGNAL_OK</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div id="sky-icon-main" class="text-4xl filter drop-shadow-sm">
                            <i class="fa-solid fa-cloud text-slate-300"></i>
                        </div>
                        <div>
                            <div id="sky-temp" class="text-3xl font-black text-slate-800 font-mono tracking-tighter skeleton w-20 h-10 mb-1"></div>
                            <div id="sky-desc" class="text-[9px] font-bold text-cyan-600 uppercase tracking-widest mt-1 skeleton w-24 h-3"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-slate-50">
                        <div>
                            <div class="text-[7px] uppercase font-black text-slate-400 mb-0.5">Humidity</div>
                            <div id="sky-humidity" class="text-[10px] font-mono font-bold text-slate-700">--</div>
                        </div>
                        <div>
                            <div class="text-[7px] uppercase font-black text-slate-400 mb-0.5">Pressure</div>
                            <div id="sky-pressure" class="text-[10px] font-mono font-bold text-slate-700">--</div>
                        </div>
                        <div>
                            <div class="text-[7px] uppercase font-black text-slate-400 mb-0.5">Wind</div>
                            <div id="sky-wind" class="text-[10px] font-mono font-bold text-slate-700">--</div>
                        </div>
                    </div>
                </div>

                <!-- IT Sentinel Map -->
                <div class="v-reveal-bottom delay-3 bg-white border border-slate-100 rounded-[1.5rem] p-2 relative h-[250px] flex flex-col shadow-lg overflow-hidden">
                    <div class="absolute top-4 left-4 z-[400] bg-white/90 backdrop-blur-sm border border-slate-200 px-3 py-1.5 rounded-lg flex items-center space-x-2 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                        <span class="text-[9px] font-mono text-slate-600 uppercase tracking-widest">UPLINK_SATELIT</span>
                    </div>
                    <div id="sentinel-map" class="w-full flex-1 rounded-[1rem] z-0 bg-slate-100"></div>
                </div>

                <!-- AI ETA Predictions -->
                <div class="v-reveal-bottom delay-5 bg-white border border-slate-100 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500" id="it-eta-card">
                    <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-brain text-9xl text-slate-300"></i>
                    </div>
                    <h3 class="text-[9px] text-slate-500 font-black mb-5 uppercase tracking-[0.2em] flex items-center border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-blue-500"></i> Matriks AI Prediktif
                    </h3>
                    
                    <div class="space-y-6 relative z-10">
                        <div>
                            <span class="text-[8px] font-mono text-slate-400 uppercase block mb-1">Estimasi Waktu Meluap</span>
                            <div class="text-3xl font-black text-emerald-500 font-mono" id="eta-overflow">STABLE</div>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-[8px] font-mono text-slate-400 uppercase block mb-2">Tingkat Kepercayaan</span>
                            <div class="flex items-center space-x-3">
                                <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-400 h-2 rounded-full" style="width: 94%"></div>
                                </div>
                                <span class="text-[10px] font-mono text-blue-500 font-bold">94.2%</span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="text-[8px] font-mono text-slate-400 uppercase block mb-2">Model Regresi</span>
                            <div class="text-[9px] font-mono text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-200">
                                Y = &beta;₀ + &beta;₁X + &epsilon;<br>
                                <span class="text-slate-400">Node Pemrosesan:</span> GPU_04<br>
                                <span class="text-slate-400">Status:</span> <span class="text-emerald-500">OPTIMAL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- WATER LEVEL HISTORY MATRIX -->
        <div class="v-reveal-bottom delay-8 mt-6">
            <div class="glass-panel rounded-[2.5rem] p-6 lg:p-8 bg-white shadow-2xl border border-slate-100 overflow-hidden relative">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-slate-100 rounded-full blur-3xl"></div>
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8 relative z-10">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase flex items-center">
                            <i class="fa-solid fa-microchip mr-3 text-cyan-600"></i> Data Telemetri <span class="text-cyan-600 ml-2" id="history-device-name">{{ $primaryDevice->name ?? 'NOC-SYSTEM' }}</span>
                        </h2>
                        <p class="text-slate-500 text-[10px] font-mono tracking-[0.3em] mt-1 uppercase">Analitik Histori Dataset Lanjutan</p>
                    </div>

                    <!-- Range Selectors -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="bg-slate-50 p-1.5 rounded-2xl flex items-center space-x-1 border border-slate-200/50">
                            <button onclick="updateHistoryRange('daily', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 bg-white text-cyan-600 shadow-sm border border-cyan-100">Harian</button>
                            <button onclick="updateHistoryRange('weekly', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 text-slate-500 hover:text-cyan-500">Mingguan</button>
                            <button onclick="updateHistoryRange('monthly', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 text-slate-500 hover:text-cyan-500">Bulanan</button>
                            <button onclick="updateHistoryRange('yearly', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 text-slate-500 hover:text-cyan-500">Tahunan</button>
                        </div>
                        
                        <!-- Custom Range Picker -->
                        <div class="flex items-center space-x-2 bg-slate-50 border border-slate-200 rounded-2xl p-1">
                            <input type="date" id="history-start-date" class="bg-transparent border-none text-[10px] font-bold text-slate-600 focus:ring-0 p-1">
                            <span class="text-slate-300 text-[10px] font-bold">TO</span>
                            <input type="date" id="history-end-date" class="bg-transparent border-none text-[10px] font-bold text-slate-600 focus:ring-0 p-1">
                            <button onclick="updateHistoryRange('custom', this)" class="bg-cyan-600 text-white p-2 rounded-xl hover:bg-cyan-700 transition-colors shadow-lg shadow-cyan-500/30">
                                <i class="fa-solid fa-radar text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Chart Canvas (Light Theme) -->
                <div class="relative h-[400px] w-full bg-slate-50/50 rounded-3xl p-6 border border-slate-100 shadow-inner group overflow-hidden">
                    <div id="history-chart-loading" class="absolute inset-0 z-20 flex items-center justify-center bg-white/40 backdrop-blur-sm hidden">
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-[9px] font-mono text-cyan-600 uppercase tracking-widest mt-4">FETCHING_HISTORICAL_DATA...</span>
                        </div>
                    </div>
                    <canvas id="historyMainChart"></canvas>
                </div>
                
                <!-- Bottom Stats Breakdown -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                        <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">AVG_TMA_MDPL</div>
                        <div class="text-2xl font-black text-slate-700 font-mono" id="hist-avg-tma">--</div>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl border-l-4 border-l-emerald-500">
                        <div class="text-[8px] font-black text-emerald-600 uppercase tracking-widest mb-1">MAX_PEAK_RECORDS</div>
                        <div class="text-2xl font-black text-slate-700 font-mono" id="hist-max-tma">--</div>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl border-l-4 border-l-blue-500">
                        <div class="text-[8px] font-black text-blue-600 uppercase tracking-widest mb-1">MIN_BASE_RECORDS</div>
                        <div class="text-2xl font-black text-slate-700 font-mono" id="hist-min-tma">--</div>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                        <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">SAMPLE_DATA_POINTS</div>
                        <div class="text-2xl font-black text-slate-700 font-mono" id="hist-sample-count">--</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modals -->
    @include('partials.dashboard.calibration_modal')

    <!-- System Scripts -->
    @include('partials.dashboard.scripts')

    <!-- IT Specific Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // --- 1. Terminal Log Simulator ---
            const terminal = document.querySelector('#it-terminal > div');
            const logs = [
                "[NET] Menerima paket dari 192.168.1.104 ukuran=42B",
                "[SYS] Pembersihan memori berjalan. Bebas 12MB.",
                "[DB] Waktu eksekusi kueri: 1.2ms.",
                "[SENSOR] Ping ultrasonik dikonfirmasi dalam 0.4d.",
                "[AI] Menghitung ulang varians lintasan prediksi...",
                "[WS] Menyiarkan 'SensorDataUpdated' -> 1204 klien.",
                "[NODE] Tegangan stabil pada 5.02V.",
                "[KEAM] Firewall menahan pemindaian port dari 45.x.x.x.",
                "[GIS] Sinkronisasi orbit satelit Sentinel-2A selesai."
            ];
            
            setInterval(() => {
                const connectionText = document.getElementById('connection-text')?.textContent;
                if(connectionText && connectionText.includes('OFFLINE')) return; // Berhenti jika offline

                const newLog = document.createElement('div');
                newLog.className = 'text-slate-500';
                const randLog = logs[Math.floor(Math.random() * logs.length)];
                const time = new Date().toISOString().split('T')[1].substring(0, 8);
                newLog.textContent = `[${time}] ${randLog}`;
                
                terminal.appendChild(newLog);
                if (terminal.children.length > 50) { // Biarkan sedikit lebih panjang sebelum dihapus
                    terminal.removeChild(terminal.firstChild);
                }
                
                // Auto-scroll ke bawah pada container terminal (bukan halaman)
                const terminalContainer = document.getElementById('it-terminal');
                if(terminalContainer) {
                    terminalContainer.scrollTop = terminalContainer.scrollHeight;
                }
            }, 2000);

            // --- 2. Micro Metrics Randomizer ---
            setInterval(() => {
                const globalStatus = document.getElementById('global-status-text')?.textContent;
                const isOffline = globalStatus && (globalStatus.includes('OFFLINE') || globalStatus.includes('DISCONNECTED'));

                if(isOffline) {
                    document.getElementById('it-uptime').textContent = '0%';
                    document.getElementById('it-ping').textContent = '0';
                    document.getElementById('it-qps').textContent = '0';
                    document.getElementById('it-voltage').textContent = '0.00 V';
                    document.getElementById('it-connections').textContent = '0';
                    document.getElementById('it-memory').textContent = '-- / --';
                    document.getElementById('it-cpu-temp').textContent = '--';
                    if(document.getElementById('it-cpu-bar')) document.getElementById('it-cpu-bar').style.width = '0%';
                    if(document.getElementById('it-packet-loss')) document.getElementById('it-packet-loss').textContent = '100%';
                    if(document.getElementById('it-packet-bar')) document.getElementById('it-packet-bar').style.width = '0%';
                    if(document.getElementById('it-signal-strength')) document.getElementById('it-signal-strength').textContent = 'NO_SIGNAL';
                    if(document.getElementById('it-signal-bar')) document.getElementById('it-signal-bar').style.width = '0%';
                    return;
                }
                
                // Animasi saat Online
                document.getElementById('it-ping').textContent = Math.floor(Math.random() * 5) + 10;
                document.getElementById('it-qps').textContent = Math.floor(Math.random() * 100) + 400;
                document.getElementById('it-voltage').textContent = (5.00 + Math.random() * 0.1).toFixed(2) + ' V';
                document.getElementById('it-connections').textContent = (1200 + Math.floor(Math.random() * 50)).toLocaleString();
                
                const cpuVal = 45 + Math.floor(Math.random() * 10);
                document.getElementById('it-cpu-temp').textContent = cpuVal + '°C';
                document.getElementById('it-cpu-bar').style.width = cpuVal + '%';

                const signalVal = 70 + Math.floor(Math.random() * 15);
                document.getElementById('it-signal-strength').textContent = '-' + (100 - signalVal) + ' dBm';
                document.getElementById('it-signal-bar').style.width = signalVal + '%';
            }, 1000);

            // --- 3. REAL-TIME HEARTBEAT CHECK ---
            function checkDeviceHeartbeat() {
                fetch('/api/devices/heartbeat')
                    .then(response => response.json())
                    .then(data => {
                        let anyOnline = false;
                        data.devices.forEach(device => {
                            const dot = document.getElementById(`status-dot-${device.slug}`);
                            if (dot) {
                                if (device.is_online) {
                                    dot.className = 'w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)] shrink-0';
                                    anyOnline = true;
                                } else {
                                    dot.className = 'w-2 h-2 rounded-full bg-red-500 shrink-0';
                                }
                            }
                            
                            // Jika ini adalah device yang sedang aktif dilihat
                            const activeDeviceName = document.getElementById('active-device-name')?.textContent;
                            if (activeDeviceName === device.name) {
                                updateGlobalBadge(device.is_online);
                            }
                        });
                    })
                    .catch(error => console.error('Heartbeat Error:', error));
            }

            function updateGlobalBadge(isOnline) {
                const badge = document.getElementById('global-connectivity-badge');
                const dot = document.getElementById('global-status-dot');
                const ping = document.getElementById('global-status-ping');
                const text = document.getElementById('global-status-text');

                if (isOnline) {
                    badge.className = 'flex items-center space-x-3 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl shadow-sm transition-all duration-500';
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 block';
                    ping.className = 'absolute inset-0 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping opacity-75';
                    text.className = 'text-[10px] font-black text-emerald-700 uppercase tracking-widest';
                    text.textContent = 'SENTINEL_ONLINE';
                } else {
                    badge.className = 'flex items-center space-x-3 bg-red-50 border border-red-100 px-4 py-2 rounded-xl shadow-sm transition-all duration-500';
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-red-500 block';
                    ping.className = 'hidden';
                    text.className = 'text-[10px] font-black text-red-700 uppercase tracking-widest';
                    text.textContent = 'SENTINEL_OFFLINE';
                }
            }

            // Start Heartbeat Polling
            setInterval(checkDeviceHeartbeat, 5000);
            checkDeviceHeartbeat();



            // --- 4. IT ETA Card Observer (Light Theme Reactive) ---
            const etaOverflow = document.getElementById('eta-overflow');
            const etaCard = document.getElementById('it-eta-card');
            
            if(etaOverflow && etaCard) {
                const observer = new MutationObserver(() => {
                    const text = etaOverflow.textContent;
                    if(text === 'STABLE' || text.includes('>1 Jam')) {
                        etaCard.className = 'bg-white border border-slate-100 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500';
                        etaOverflow.className = 'text-3xl font-black text-emerald-500 font-mono';
                    } else if(text === 'IMMINENT') {
                        etaCard.className = 'bg-red-50 border border-red-200 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500 animate-pulse';
                        etaOverflow.className = 'text-3xl font-black text-red-600 font-mono drop-shadow-sm';
                    } else {
                        etaCard.className = 'bg-orange-50 border border-orange-200 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500';
                        etaOverflow.className = 'text-3xl font-black text-orange-500 font-mono drop-shadow-sm';
                    }
                });
                observer.observe(etaOverflow, { childList: true, characterData: true, subtree: true });
            }
        });

        // Trigger Animations with 'Human-Eye' Sync Delay
        function triggerReveal() {
            setTimeout(() => {
                if(!document.body.classList.contains('loaded')) {
                    document.body.classList.add('loaded'); 
                }
            }, 300); // 300ms delay to ensure visibility on fast desktop devices
        }

        window.addEventListener('DOMContentLoaded', triggerReveal);
        window.addEventListener('load', triggerReveal); // Backup
        setTimeout(triggerReveal, 3000); // EMERGENCY FAIL-SAFE (3 Seconds)

        // Device Selector Logic (IT)
        function toggleDeviceSelector() {
            const menu = document.getElementById('device-selector-menu');
            const icon = document.getElementById('device-selector-icon');
            if (menu.classList.contains('invisible')) {
                menu.classList.remove('invisible', 'scale-y-0', 'opacity-0');
                menu.classList.add('scale-y-100', 'opacity-100');
                icon.classList.add('rotate-180');
            } else {
                menu.classList.remove('scale-y-100', 'opacity-100');
                menu.classList.add('scale-y-0', 'opacity-0');
                icon.classList.remove('rotate-180');
                setTimeout(() => menu.classList.add('invisible'), 300);
            }
        }

        function userSwitchDevice(slug, name, location, lat, lng) {
            document.getElementById('active-device-name').textContent = name;
            toggleDeviceSelector();
            if(typeof window.switchDevice === 'function') {
                window.switchDevice(slug, name, lat, lng);
            }
        }

        // Close on outside click
        document.addEventListener('click', (e) => {
            if(!document.getElementById('device-selector-wrapper')?.contains(e.target)) {
                const menu = document.getElementById('device-selector-menu');
                if(menu && !menu.classList.contains('invisible')) toggleDeviceSelector();
            }
        });

    </script>
    
    <!-- System Scripts -->
    @include('partials.dashboard.scripts')
    @include('partials.dashboard.history_scripts')
</body>
</html>
