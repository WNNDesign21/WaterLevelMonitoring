<div class="md:col-span-8 lg:col-span-6 flex flex-col space-y-6">
    <!-- Main KPI & Alert Card -->
    <div id="status-card" class="glass-panel rounded-3xl p-8 relative overflow-hidden transition-all duration-500">
        <!-- Decoration Glow -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-400/10 blur-[60px] rounded-full"></div>
        
        <div class="flex justify-between items-start mb-6 relative z-10">
            <div>
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold tracking-wider rounded-full uppercase mb-2 border border-blue-100/50">Pos Pantau: Kedunggede, S. Citarum</span>
                <div class="text-sm text-slate-400 font-medium tracking-tight">Terakhir diperbarui: <span class="font-mono text-slate-700 font-bold ml-1" id="last-updated">--:--:--</span></div>
            </div>
            <div id="alert-banner" class="px-4 py-2 rounded-xl flex items-center space-x-2 transition-all duration-300 bg-emerald-50 text-emerald-600 border border-emerald-100 hidden">
                <i id="alert-icon" class="fa-solid fa-shield-check"></i>
                <span id="alert-message" class="font-bold text-xs tracking-wide uppercase">Status Aman</span>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row items-center justify-between border-t border-slate-100 pt-6 relative z-10">
            <div class="text-center md:text-left mb-4 md:mb-0">
                <h3 class="text-sm text-slate-500 font-medium mb-1 uppercase tracking-widest text-[10px]">Tinggi Muka Air (TMA)</h3>
                <div class="flex items-baseline justify-center md:justify-start">
                    <div id="water-level" class="text-7xl lg:text-8xl font-black text-slate-800 odometer tracking-tight leading-none font-rajdhani">--</div>
                    <span class="text-2xl text-blue-500 font-bold ml-2">MDPL</span>
                </div>
            </div>
            <div class="hidden md:block w-px h-16 bg-gradient-to-b from-transparent via-slate-200 to-transparent"></div>
            <div class="text-center md:text-right">
                <h3 class="text-sm text-slate-500 font-medium mb-1 uppercase tracking-widest text-[10px]">Jarak Udara</h3>
                <div class="flex items-baseline justify-center md:justify-end">
                    <span id="current-distance" class="text-4xl font-bold text-slate-400 odometer leading-none font-rajdhani">--</span>
                    <span class="text-lg text-slate-400 ml-1">cm</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Animated River View Section -->
    <div class="glass-panel rounded-[3rem] p-4 flex-1 relative group min-h-[500px] bg-slate-50 shadow-inner">
        <div class="river-container border-2 border-slate-200/50 shadow-2xl relative">
            <div class="river-bank-left"></div>
            <div class="river-bank-right"></div>
            
            <!-- Accessibility Zone Patterns -->
            <div class="absolute inset-x-0 bottom-0 z-0 flex flex-col-reverse h-full pointer-events-none rounded-[2rem] overflow-hidden">
                <div class="pattern-overlay pattern-siaga1 h-[41.7%]" style="bottom: 58.3%"></div>
                <div class="pattern-overlay pattern-siaga2 h-[8.3%]" style="bottom: 50%"></div>
                <div class="pattern-overlay pattern-siaga3 h-[16.7%]" style="bottom: 33.3%"></div>
            </div>
            
            <!-- Water Layer (Clip Path used to avoid global overflow hidden) -->
            <div class="river-water" id="river-water" style="height: 0%; z-index: 10; border-bottom-left-radius: 1.8rem; border-bottom-right-radius: 1.8rem; overflow: hidden;">
                <div class="absolute inset-0 bg-blue-900/5 mix-blend-overlay"></div>
                <!-- Dynamic Percent Label -->
                <div id="water-percent-container" class="absolute inset-x-0 top-1/2 -translate-y-1/2 text-center transition-all duration-700 pointer-events-none">
                    <div id="water-percent" class="text-8xl font-black text-white/5 uppercase tracking-tighter mix-blend-overlay transition-all duration-500">0%</div>
                </div>
            </div>

            <!-- Floating Surface Badge (Moved to separate foreground layer for better clamping) -->
            <div id="surface-badge" class="surface-badge" style="bottom: 0px; transform: translateY(50%);">
                <i id="surface-icon" class="fa-solid fa-circle-check"></i>
                <span id="surface-text">NORMAL</span>
            </div>

            <!-- Foreground Overlays (High Contrast) -->
            <div class="absolute inset-0 z-40 pointer-events-none">
                <!-- Vertical Scale Ruler -->
                @for($i = 0; $i <= 600; $i += 50)
                    @php 
                        $bottomPercent = ($i / 600) * 100; 
                        $tmaValue = 8.00 + ($i / 100);
                    @endphp
                    @if($i % 100 == 0)
                        <div class="absolute right-0 left-0 h-px bg-slate-900/[0.08]" style="bottom: {{ $bottomPercent }}%"></div>
                        <div class="depth-tick !w-16 !bg-slate-900" style="bottom: {{ $bottomPercent }}%"></div>
                        <div class="depth-value" style="bottom: {{ $bottomPercent }}%; transform: translateY(50%)">{{ number_format($tmaValue, 2) }}</div>
                    @else
                        <div class="depth-tick" style="bottom: {{ $bottomPercent }}%"></div>
                    @endif
                @endfor

                <!-- Status Labels -->
                <div id="label-normal" class="level-label text-emerald-600 active" style="bottom: 16%"><i class="fa-solid fa-circle-check"></i> NORMAL</div>
                <div id="label-siaga3" class="level-label text-yellow-600 opacity-30" style="bottom: 41%"><i class="fa-solid fa-triangle-exclamation"></i> SIAGA 3</div>
                <div id="label-siaga2" class="level-label text-orange-600 opacity-30" style="bottom: 54%"><i class="fa-solid fa-bell"></i> SIAGA 2</div>
                <div id="label-siaga1" class="level-label text-red-600 opacity-30" style="bottom: 79%"><i class="fa-solid fa-skull-crossbones"></i> SIAGA 1</div>
            </div>
        </div>
    </div>
</div>
