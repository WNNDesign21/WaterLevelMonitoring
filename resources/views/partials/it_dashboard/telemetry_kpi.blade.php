<!-- CENTER COLUMN: Raw Telemetry & Visual Tank -->
<div class="lg:col-span-6 flex flex-col space-y-5">
    <div class="v-reveal-right delay-9 bg-white border border-slate-100 rounded-[2rem] p-6 relative overflow-hidden shadow-xl flex flex-col">
        <div class="absolute -right-10 -bottom-10 opacity-5 pointer-events-none">
            <i class="fa-solid fa-water text-9xl"></i>
        </div>

        <!-- Universal Node Selector -->
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
            <div class="col-span-3 lg:col-span-1 border-b lg:border-b-0 lg:border-r border-slate-100 pb-4 lg:pb-0">
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
        <div class="mt-6 h-20 w-full relative z-10">
            <canvas id="tma-sparkline"></canvas>
        </div>
    </div>

    <!-- Visual Simulation Tank -->
    <div class="v-reveal-left delay-7 bg-white border border-slate-100 rounded-[2rem] p-3 flex-1 relative min-h-[350px] shadow-inner group">
        <div class="absolute top-5 left-5 z-[100] bg-white border border-slate-200 px-3 py-1.5 rounded-lg flex items-center space-x-2 shadow-sm">
            <i class="fa-solid fa-layer-group text-slate-400 text-[10px]"></i>
            <span class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Simulasi Visual</span>
        </div>
        <div class="glass-tank-container relative w-full h-full rounded-[1.5rem] bg-slate-50 border border-slate-200 overflow-hidden shadow-[inset_0_0_30px_rgba(0,0,0,0.05)]">
            <div class="liquid-water transition-all duration-700 ease-out" id="river-water" style="height: 0%;">
                <div class="liquid-water-particles"></div>
                <div id="water-percent-container" class="absolute inset-x-0 top-1/2 -translate-y-1/2 text-center transition-all duration-700 pointer-events-none z-20">
                    <div id="water-percent" class="text-6xl font-black text-white/20 uppercase tracking-tighter mix-blend-overlay font-mono">0%</div>
                </div>
            </div>
            <div class="absolute inset-0 z-40 pointer-events-none">
                @for($i = 0; $i <= 600; $i += 50)
                    @php $bottomPercent = ($i / 600) * 100; $tmaValue = 8.00 + ($i / 100); @endphp
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
