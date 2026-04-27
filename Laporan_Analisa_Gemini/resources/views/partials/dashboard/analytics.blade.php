@if(!isset($showOnlyWeather) || $showOnlyWeather)
<div class="mt-6">
    <!-- Sky Sentinel (Luxury Weather Grade) -->
    <div class="glass-panel rounded-[40px] p-8 relative overflow-hidden mb-6 border-white/40 shadow-2xl shadow-blue-500/10 bg-gradient-to-br from-white/80 to-blue-50/50 backdrop-blur-3xl">
        <!-- Dynamic Weather Background Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-400/10 rounded-full blur-[100px] -mr-48 -mt-48 transition-all duration-1000" id="weather-bg-glow"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <!-- Main Temp & Location -->
                <div class="flex items-center bg-white/40 p-6 rounded-[32px] border border-white/60 shadow-inner">
                    <div id="sky-icon-main" class="text-7xl mr-6 filter drop-shadow-xl animate-float">
                        <i class="fa-solid fa-cloud-sun text-amber-400"></i>
                    </div>
                    <div>
                        <div class="flex items-baseline space-x-2">
                            <h1 id="sky-temp" class="text-6xl font-black text-slate-800 tracking-tighter">--°C</h1>
                            <span class="text-xl font-bold text-slate-400">FEELS LIKE <span id="sky-feels">--°</span></span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <i class="fa-solid fa-location-dot text-blue-500 animate-bounce"></i>
                            <span id="sky-location-detail" class="text-sm font-black text-slate-500 uppercase tracking-widest leading-none">
                                {{ $primaryDevice->latitude }}, {{ $primaryDevice->longitude }}
                            </span>
                        </div>
                        <div id="sky-desc" class="text-lg font-bold text-blue-600/80 mt-1">Synchronizing Sky Sentinel...</div>
                    </div>
                </div>

                <!-- Detailed Metrics (AccuWeather Style) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 flex-1">
                    <div class="p-4 rounded-3xl bg-white/60 border border-white/80 shadow-sm group hover:scale-105 transition-transform duration-300">
                        <div class="flex items-center space-x-2 text-blue-500 mb-2">
                            <i class="fa-solid fa-wind text-sm"></i>
                            <span class="text-[10px] font-black uppercase tracking-tighter">Wind Speed</span>
                        </div>
                        <div id="sky-wind" class="text-xl font-black text-slate-800">-- <span class="text-[10px] text-slate-400">KM/H</span></div>
                        <div class="w-full h-1 bg-slate-100 rounded-full mt-2 overflow-hidden">
                            <div class="h-full bg-blue-500 w-[0%]"></div>
                        </div>
                    </div>
                    <div class="p-4 rounded-3xl bg-white/60 border border-white/80 shadow-sm group hover:scale-105 transition-transform duration-300">
                        <div class="flex items-center space-x-2 text-amber-500 mb-2">
                            <i class="fa-solid fa-sun text-sm"></i>
                            <span class="text-[10px] font-black uppercase tracking-tighter">UV Index</span>
                        </div>
                        <div id="sky-uv" class="text-xl font-black text-slate-800">-- <span class="text-[10px] text-slate-400">SYNC</span></div>
                        <div class="w-full h-1 bg-slate-100 rounded-full mt-2 overflow-hidden">
                            <div class="h-full bg-amber-500 w-[0%]"></div>
                        </div>
                    </div>
                    <div class="p-4 rounded-3xl bg-white/60 border border-white/80 shadow-sm group hover:scale-105 transition-transform duration-300">
                        <div class="flex items-center space-x-2 text-cyan-500 mb-2">
                            <i class="fa-solid fa-droplet text-sm"></i>
                            <span class="text-[10px] font-black uppercase tracking-tighter">Humidity</span>
                        </div>
                        <div id="sky-humidity" class="text-xl font-black text-slate-800">-- <span class="text-[10px] text-slate-400">%</span></div>
                        <div class="w-full h-1 bg-slate-100 rounded-full mt-2 overflow-hidden">
                            <div class="h-full bg-cyan-500 w-[0%]"></div>
                        </div>
                    </div>
                    <div class="p-4 rounded-3xl bg-white/60 border border-white/80 shadow-sm group hover:scale-105 transition-transform duration-300">
                        <div class="flex items-center space-x-2 text-indigo-500 mb-2">
                            <i class="fa-solid fa-gauge-high text-sm"></i>
                            <span class="text-[10px] font-black uppercase tracking-tighter">Pressure</span>
                        </div>
                        <div id="sky-pressure" class="text-xl font-black text-slate-800">-- <span class="text-[10px] text-slate-400">HPA</span></div>
                        <div class="text-[8px] font-bold text-indigo-400 mt-1 uppercase">Stabilizing HUD...</div>
                    </div>
                </div>
            </div>

            <!-- Mini Forecast Bar -->
            <div class="mt-8 pt-6 border-t border-slate-200/50 flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-4 shrink-0">3-Hour Outlook</div>
                @for ($i = 1; $i <= 6; $i++)
                <div class="flex flex-col items-center bg-white/30 px-5 py-3 rounded-2xl border border-white/40 shrink-0">
                    <span class="text-[9px] font-black text-slate-500 mb-2">+{{ $i * 3 }}H</span>
                    <i class="fa-solid fa-cloud-sun text-amber-400 text-lg mb-2"></i>
                    <span class="text-sm font-black text-slate-800">--°</span>
                </div>
                @endfor
                <div class="flex-1"></div>
            </div>
        </div>
    </div>
</div>
@endif

@if(!isset($showOnlyWeather) || !$showOnlyWeather)
<div class="mt-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Telemetry Graph (Takes 2 columns) -->
    <div class="glass-panel rounded-3xl p-6 md:col-span-2">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-[10px] font-black tracking-[0.2em] text-slate-400 flex items-center uppercase">
                <div class="w-6 h-6 rounded-lg bg-cyan-500 text-white flex items-center justify-center mr-3 shadow-lg shadow-cyan-500/20">
                    <i class="fa-solid fa-chart-area text-[10px]"></i>
                </div>
                Depth Histogram Matrix
            </h2>
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-1.5 px-2 py-1 bg-slate-900 rounded-lg border border-blue-500/30 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    <span class="text-[9px] font-black text-blue-400 uppercase tracking-tighter">AI SENTINEL ACTIVE</span>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Signal Buffer: Full</span>
            </div>
        </div>
        <div class="h-64">
            <canvas id="depthChart"></canvas>
        </div>
    </div>

    <!-- Statistics & Prediction Card -->
    <div class="glass-panel rounded-3xl p-6 bg-gradient-to-br from-white to-slate-50 relative overflow-hidden">
        <h2 class="text-[10px] font-black tracking-[0.2em] text-slate-400 mb-8 flex items-center uppercase">
            <div class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center mr-3 shadow-xl">
                <i class="fa-solid fa-bolt-lightning text-[10px]"></i>
            </div>
            Geotagging & Analytics HUD
        </h2>
        
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm">
                    <div class="text-[10px] font-black text-slate-400 uppercase mb-1">Peak Level</div>
                    <div class="text-lg font-bold text-slate-700" id="max-peak-level">0</div>
                    <div class="text-[8px] text-slate-400 uppercase font-black">Session Record</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm">
                    <div class="text-[10px] font-black text-slate-400 uppercase mb-1">Base Level</div>
                    <div class="text-lg font-bold text-slate-700" id="min-base-level">0</div>
                    <div class="text-[8px] text-slate-400 uppercase font-black">Session Minimum</div>
                </div>
            </div>

            <div id="weather-correlation-alert" class="p-4 rounded-2xl bg-slate-900 text-white hidden animate-pulse border border-blue-500/30">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-cloud-showers-heavy text-blue-400 text-xl"></i>
                    <div>
                        <div class="text-[9px] font-black text-blue-400 uppercase leading-none mb-1">Precipitation Warning</div>
                        <div class="text-[10px] font-bold text-slate-300">High rain intensity correlated with rising water levels.</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm">
                    <div class="text-[10px] font-black text-slate-400 uppercase mb-1">Velocity Map</div>
                    <div id="flow-velocity" class="text-lg font-bold text-slate-700">0.00</div>
                    <div class="text-[8px] text-slate-400 uppercase font-black">cm/sec flux</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm">
                    <div class="text-[10px] font-black text-slate-400 uppercase mb-1">ETA Alert</div>
                    <div id="eta-overflow" class="text-lg font-bold text-slate-700">STABLE</div>
                    <div class="text-[8px] text-slate-400 uppercase font-black">Time to Overflow</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-blue-600 shadow-lg shadow-blue-600/20 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <div class="text-[10px] font-black text-blue-200 uppercase mb-3 tracking-widest">Active Geotagging</div>
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-location-dot text-2xl text-blue-200"></i>
                        <div>
                            <div class="text-sm font-bold leading-none mb-1">{{ $primaryDevice->location ?? 'Unknown' }}</div>
                            <div class="text-[9px] text-blue-200/80 font-medium leading-tight mb-2" id="current-address-text">SATELLITE POSITION LOCKED</div>
                            <div class="text-[10px] text-blue-100/70 font-mono" id="current-coords-text">{{ $primaryDevice->latitude }}, {{ $primaryDevice->longitude }}</div>
                        </div>
                    </div>
                </div>
                <i class="fa-solid fa-earth-asia absolute -right-4 -bottom-4 text-8xl text-blue-500/20 rotate-12"></i>
            </div>
        </div>
    </div>
    </div>
</div>
@endif
