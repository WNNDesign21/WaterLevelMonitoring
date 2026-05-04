@include('partials.dashboard.head')

<body class="min-h-screen relative antialiased selection:bg-blue-200 selection:text-blue-900 pb-10 bg-slate-50 overflow-x-hidden">

    <!-- Top Accent Line -->
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500 z-50"></div>

    <!-- Main Container -->
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 mt-4 relative z-10">
        
        <!-- Header Section -->
        @include('partials.dashboard.header')

        <!-- Unified Cockpit Interface -->
        <div class="glass-panel rounded-[2.5rem] p-4 lg:p-6 mt-4 bg-white/60 shadow-2xl backdrop-blur-2xl border border-white/80 animate-assemble-bg" style="opacity: 0; perspective: 2500px;">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch transform-style-3d">
                
                <!-- Column 1: Weather & Guides (3 Cols) -->
                <div class="lg:col-span-3 flex flex-col space-y-4 animate-assemble-left" style="opacity: 0;">
                    
                    <!-- Title inside the cockpit -->
                    <div class="mb-4 flex flex-col space-y-4">
                        <div>
                            <h2 class="text-xl font-black text-slate-800 tracking-tight uppercase">Pantauan Publik</h2>
                            <p class="text-slate-500 text-xs font-bold tracking-widest mt-1">SISTEM INFORMASI CITARUM</p>
                        </div>
                        
                        <!-- LUXURIOUS DEVICE SELECTOR -->
                        <div class="relative w-full z-40 group" id="device-selector-wrapper">
                            <button type="button" onclick="toggleDeviceSelector()" id="device-selector-btn" class="w-full bg-white/80 backdrop-blur-md border border-slate-200/60 shadow-lg shadow-blue-900/5 hover:shadow-blue-900/10 rounded-2xl p-4 flex items-center justify-between transition-all duration-300">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shrink-0 shadow-inner">
                                        <i class="fa-solid fa-satellite-dish text-white text-sm"></i>
                                    </div>
                                    <div class="text-left overflow-hidden">
                                        <div class="text-[9px] uppercase font-black text-blue-500 tracking-widest mb-0.5">Lokasi Sensor Aktif</div>
                                        <div class="text-sm font-bold text-slate-800 truncate" id="active-device-name">{{ $primaryDevice->name ?? 'Pilih Sensor' }}</div>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 shrink-0 transition-transform duration-300" id="device-selector-icon">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="device-selector-menu" class="absolute top-full left-0 w-full mt-2 bg-white/95 backdrop-blur-2xl border border-white/80 shadow-2xl rounded-2xl overflow-hidden transition-all duration-300 origin-top transform scale-y-0 opacity-0 invisible max-h-[350px] overflow-y-auto" style="scrollbar-width: thin;">
                                <div class="p-2 space-y-1">
                                    @if(isset($allDevices) && count($allDevices) > 0)
                                        @foreach($allDevices as $device)
                                        <button type="button" onclick="userSwitchDevice('{{ $device->slug }}', '{{ addslashes($device->name) }}', '{{ addslashes($device->location) }}')" class="w-full text-left p-3 rounded-xl hover:bg-blue-50/80 transition-colors flex items-center space-x-3 group/item">
                                            <div class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-red-500' }} shrink-0"></div>
                                            <div class="flex-1 overflow-hidden">
                                                <div class="text-xs font-bold text-slate-800 truncate group-hover/item:text-blue-600 transition-colors">{{ $device->name }}</div>
                                                <div class="text-[9px] text-slate-400 uppercase tracking-wider truncate mt-0.5"><i class="fa-solid fa-location-crosshairs mr-1"></i> {{ $device->location ?: 'Lokasi Tidak Diketahui' }}</div>
                                            </div>
                                        </button>
                                        @endforeach
                                    @else
                                        <div class="p-4 text-center text-xs text-slate-400">Tidak ada sensor tersedia</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compact Weather -->
                    <div class="rounded-3xl p-5 relative overflow-hidden bg-gradient-to-br from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-500/30 group">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all"></div>
                        <div class="relative z-10">
                            <div class="flex items-center space-x-2 mb-3">
                                <i class="fa-solid fa-location-dot text-cyan-200 animate-bounce text-sm"></i>
                                <span id="sky-location-detail" class="text-[10px] font-black uppercase tracking-widest truncate">Lokasi...</span>
                            </div>
                            <div class="flex items-center space-x-4 mb-4">
                                <div id="sky-icon-main" class="text-5xl filter drop-shadow-md"><i class="fa-solid fa-cloud-sun text-amber-300"></i></div>
                                <div>
                                    <div id="sky-temp" class="text-4xl font-black tracking-tighter leading-none">--°C</div>
                                    <div id="sky-desc" class="text-[10px] font-bold text-blue-100 uppercase tracking-widest mt-1">Memuat...</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-white/20">
                                <div class="text-center">
                                    <div class="text-[9px] uppercase font-black text-blue-200 mb-1">Angin</div>
                                    <div id="sky-wind" class="font-bold text-xs">--</div>
                                </div>
                                <div class="w-px h-6 bg-white/20"></div>
                                <div class="text-center">
                                    <div class="text-[9px] uppercase font-black text-blue-200 mb-1">Lembap</div>
                                    <div id="sky-humidity" class="font-bold text-xs">--</div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Compact Status Guide -->
                    <div class="rounded-3xl p-5 bg-white border border-slate-100 shadow-sm flex-1">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4 flex items-center">
                            <i class="fa-solid fa-circle-info mr-2"></i> Panduan
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-xs"></i></div>
                                <div><div class="text-[10px] font-black uppercase text-slate-700 leading-none">Normal</div><div class="text-[9px] text-slate-400 mt-1">Aman</div></div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation text-xs"></i></div>
                                <div><div class="text-[10px] font-black uppercase text-slate-700 leading-none">Siaga 3</div><div class="text-[9px] text-slate-400 mt-1">Waspada</div></div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-bell text-xs"></i></div>
                                <div><div class="text-[10px] font-black uppercase text-slate-700 leading-none">Siaga 2</div><div class="text-[9px] text-slate-400 mt-1">Siaga Banjir</div></div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 animate-pulse"><i class="fa-solid fa-skull-crossbones text-xs"></i></div>
                                <div><div class="text-[10px] font-black uppercase text-red-600 leading-none">Siaga 1</div><div class="text-[9px] text-red-500 font-bold mt-1">Evakuasi Kritis!</div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: The Core River Visual (5 Cols) -->
                <div class="lg:col-span-5 relative min-h-[400px] xl:min-h-[500px] flex items-stretch animate-assemble-center" style="opacity: 0;">
                    <!-- Glass Tank natively occupying the space without its own panel background -->
                    <div class="glass-tank-container relative w-full h-full rounded-[2rem] shadow-[inset_0_0_50px_rgba(0,0,0,0.05)] bg-slate-100/50 border-4 border-white/60 overflow-hidden">
                        
                        <!-- Immersive Weather Overlay -->
                        <div class="weather-rain" id="weather-rain">
                            @for($i=0; $i<40; $i++)
                                <div class="drop" style="left: {{ rand(1, 99) }}%; animation-duration: {{ 0.5 + (rand(0, 5) / 10) }}s; animation-delay: {{ rand(0, 10) / 10 }}s;"></div>
                            @endfor
                        </div>

                        <!-- Accessibility Zone Patterns -->
                        <div class="absolute inset-x-0 bottom-0 z-0 flex flex-col-reverse h-full pointer-events-none rounded-[2rem] overflow-hidden">
                            <div class="pattern-overlay pattern-siaga1 h-[41.7%]" style="bottom: 58.3%"></div>
                            <div class="pattern-overlay pattern-siaga2 h-[8.3%]" style="bottom: 50%"></div>
                            <div class="pattern-overlay pattern-siaga3 h-[16.7%]" style="bottom: 33.3%"></div>
                        </div>
                        
                        <!-- Water Layer -->
                        <div class="liquid-water" id="river-water" style="height: 0%;">
                            <div class="liquid-water-particles"></div>
                            <!-- Dynamic Percent Label -->
                            <div id="water-percent-container" class="absolute inset-x-0 top-1/2 -translate-y-1/2 text-center transition-all duration-700 pointer-events-none z-20">
                                <div id="water-percent" class="text-6xl font-black text-white/20 uppercase tracking-tighter mix-blend-overlay transition-all duration-500">0%</div>
                            </div>
                        </div>

                        <!-- Floating Surface Badge -->
                        <div id="surface-badge" class="surface-badge !text-[10px] !px-3 !py-1" style="bottom: 0px; transform: translateY(50%);">
                            <i id="surface-icon" class="fa-solid fa-circle-check"></i>
                            <span id="surface-text">NORMAL</span>
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

                <!-- Column 3: Telemetry & Alerts (4 Cols) -->
                <div class="lg:col-span-4 flex flex-col justify-center space-y-4 animate-assemble-right" style="opacity: 0;">
                    
                    <!-- Alert Banner (Integrated) -->
                    <div id="alert-banner" class="px-5 py-3 rounded-2xl flex items-center space-x-3 transition-all duration-300 bg-emerald-50 text-emerald-600 border border-emerald-100 hidden">
                        <i id="alert-icon" class="fa-solid fa-shield-check text-3xl"></i>
                        <div>
                            <div class="text-[10px] font-black tracking-widest uppercase mb-1">Status Sungai</div>
                            <div id="alert-message" class="font-black text-xl tracking-wide uppercase leading-none">Aman Terkendali</div>
                        </div>
                    </div>

                    <!-- Main KPI & Map Row -->
                    <div class="grid grid-cols-2 gap-4 flex-1">
                        <!-- Main TMA KPI (Massive) -->
                        <div class="rounded-[2rem] p-6 bg-white border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-colors flex flex-col justify-center">
                            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity">
                                <i class="fa-solid fa-water text-8xl"></i>
                            </div>
                            <h3 class="text-[10px] text-slate-400 font-black mb-3 uppercase tracking-[0.2em] relative z-10">Tinggi Permukaan Air</h3>
                            <div class="flex items-baseline mb-4 relative z-10">
                                <div id="water-level" class="text-5xl xl:text-6xl font-black text-slate-800 odometer tracking-tighter leading-none font-rajdhani">--</div>
                                <span class="text-lg text-blue-500 font-bold ml-2">MDPL</span>
                            </div>
                            <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50 inline-flex px-3 py-1.5 rounded-lg border border-slate-100 relative z-10">
                                <i class="fa-solid fa-clock text-blue-500"></i>
                                <span id="live-clock">--:--:-- WIB</span>
                            </div>

                            <!-- Real-time Sparkline -->
                            <div class="mt-4 h-16 w-full relative z-10 opacity-80">
                                <canvas id="tma-sparkline"></canvas>
                            </div>
                        </div>

                        <!-- GIS Satellite Map -->
                        <div class="rounded-[2rem] p-2 bg-white border border-slate-100 shadow-xl overflow-hidden relative flex flex-col">
                            <div class="absolute top-4 left-4 z-[400] flex items-center bg-white/80 backdrop-blur-sm px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2 animate-ping" id="map-radar-dot"></span>
                                <span class="text-[9px] font-black uppercase text-slate-600 tracking-widest">Satelit Aktif</span>
                            </div>
                            <div id="sentinel-map" class="w-full flex-1 min-h-[150px] rounded-[1.5rem] z-0 filter contrast-100 saturate-100"></div>
                        </div>
                    </div>

                    <!-- Advanced Metrics Grid (Distance, Velocity, ETA) -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-3xl p-5 bg-white border border-slate-100 shadow-sm flex flex-col justify-center">
                            <h3 class="text-[9px] text-slate-400 font-black mb-1 uppercase tracking-[0.2em] leading-tight">Jarak Sensor</h3>
                            <div class="flex items-baseline">
                                <span id="current-distance" class="text-3xl font-bold text-slate-700 odometer font-rajdhani">--</span>
                                <span class="text-xs text-slate-400 font-bold ml-1">cm</span>
                            </div>
                        </div>
                        
                        <div class="rounded-3xl p-5 bg-white border border-slate-100 shadow-sm flex flex-col justify-center">
                            <h3 class="text-[9px] text-slate-400 font-black mb-1 uppercase tracking-[0.2em] leading-tight">Laju Air</h3>
                            <div class="flex items-baseline text-blue-500 transition-colors" id="velocity-container">
                                <i class="fa-solid fa-arrow-right text-xs mr-1" id="velocity-icon"></i>
                                <span id="flow-velocity" class="text-2xl font-bold odometer font-rajdhani">0.0</span>
                                <span class="text-xs font-bold ml-1">cm/s</span>
                            </div>
                        </div>

                        <div class="col-span-2 rounded-3xl p-5 bg-white border border-slate-100 shadow-xl flex items-center justify-between relative overflow-hidden group" id="eta-card">
                            <!-- AI Decorator -->
                            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                <i class="fa-solid fa-microchip text-6xl text-blue-500"></i>
                            </div>
                            <div class="relative z-10">
                                <h3 class="text-[9px] text-blue-500 font-black mb-1 uppercase tracking-[0.2em]" id="eta-label">AI Prediksi (ETA Meluap)</h3>
                                <div class="text-xl font-bold text-slate-800 font-rajdhani" id="eta-overflow">Mengkalkulasi...</div>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100 relative z-10" id="eta-icon-wrapper">
                                <i class="fa-solid fa-shield-check text-blue-500" id="eta-icon"></i>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Scripts Application -->
    @include('partials.dashboard.scripts')
    
    <script>
        // Live Clock logic
        setInterval(() => {
            const clockEl = document.getElementById('live-clock');
            if(clockEl) {
                clockEl.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
            }
        }, 1000);

        // Device Selector Logic
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
        
        // Close when clicking outside
        document.addEventListener('click', function(event) {
            const wrapper = document.getElementById('device-selector-wrapper');
            if (wrapper && !wrapper.contains(event.target)) {
                const menu = document.getElementById('device-selector-menu');
                const icon = document.getElementById('device-selector-icon');
                if (menu && !menu.classList.contains('invisible')) {
                    menu.classList.remove('scale-y-100', 'opacity-100');
                    menu.classList.add('scale-y-0', 'opacity-0');
                    icon.classList.remove('rotate-180');
                    setTimeout(() => menu.classList.add('invisible'), 300);
                }
            }
        });

        function userSwitchDevice(slug, name, location) {
            // Update UI Title
            document.getElementById('active-device-name').textContent = name;
            
            // Close menu
            toggleDeviceSelector();
            
            // Panggil switchDevice bawaan scripts.blade.php
            if(typeof window.switchDevice === 'function') {
                window.switchDevice(slug, name);
                
                // Update URL parameter silently for shareability
                window.history.pushState({}, '', '/dashboard/' + slug);
            }
        }

        // Override weather correlation visibility logic specifically for Cockpit layout
        document.addEventListener('DOMContentLoaded', () => {
            const originalWeatherUpdate = window.updateWeatherWidget;
            if(originalWeatherUpdate) {
                window.updateWeatherWidget = function(data) {
                    originalWeatherUpdate(data);
                    // Check if rain
                    const isRaining = data.weather[0].main.toLowerCase().includes('rain') || 
                                     data.weather[0].description.toLowerCase().includes('hujan');
                    
                    const rainOverlay = document.getElementById('weather-rain');
                    if(rainOverlay) {
                        if(isRaining) {
                            rainOverlay.classList.add('active');
                        } else {
                            rainOverlay.classList.remove('active');
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>
