<div class="md:col-span-4 lg:col-span-3 flex flex-col space-y-6">
    <!-- Sentinel Health Monitoring -->
    <div class="glass-panel rounded-3xl p-6 relative overflow-hidden flex-shrink-0">
        <h2 class="text-[10px] font-black tracking-[0.2em] text-slate-400 border-b border-slate-100 pb-3 mb-4 flex items-center uppercase">
            <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center mr-3 shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-microchip text-[10px]"></i>
            </div>
            Sentinel Health System
        </h2>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-500 font-medium">Tegangan Node</span>
                <span class="text-emerald-500 font-semibold bg-emerald-50 px-2 py-1 rounded-md" id="mock-voltage">5.02V</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-500 font-medium">Latensi Jaringan</span>
                <span class="text-slate-700 font-semibold bg-slate-100 px-2 py-1 rounded-md" id="mock-ping">12ms</span>
            </div>
            <div class="flex justify-between items-center text-sm border-b border-slate-100 pb-4">
                <span class="text-slate-500 font-medium">Suhu CPU</span>
                <span class="text-amber-500 font-semibold bg-amber-50 px-2 py-1 rounded-md" id="mock-temp">42.5°C</span>
            </div>
            <div class="pt-1">
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="text-slate-500 font-medium">Integritas Data</span>
                    <span class="text-blue-600 font-bold" id="valid-count-display">0/20</span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div id="signal-bar" class="h-full bg-gradient-to-r from-blue-400 to-cyan-400 w-0 transition-all duration-300"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 24-Hour Sentinel Trend -->
    <div class="glass-panel rounded-3xl p-6 relative overflow-hidden flex-shrink-0 h-[220px]">
        <h2 class="text-[10px] font-black tracking-[0.2em] text-slate-400 border-b border-slate-100 pb-3 mb-4 flex items-center uppercase">
            <div class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center mr-3 shadow-lg">
                <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
            </div>
            Sentinel Trend Matrix
        </h2>
        
        <div id="trend-heatmap" class="flex items-end justify-between h-20 gap-1 mt-2">
            <!-- Heatmap bars will be injected here -->
            <div class="flex-1 h-2 bg-slate-100 rounded-full"></div>
            <div class="flex-1 h-4 bg-slate-100 rounded-full"></div>
            <div class="flex-1 h-8 bg-slate-100 rounded-full"></div>
            <div class="flex-1 h-12 bg-slate-100 rounded-full"></div>
            <div class="flex-1 h-6 bg-slate-100 rounded-full"></div>
            <div class="flex-1 h-3 bg-slate-100 rounded-full"></div>
            <div class="flex-1 h-10 bg-slate-100 rounded-full"></div>
            <div class="flex-1 h-14 bg-slate-100 rounded-full"></div>
        </div>
        <div class="flex justify-between mt-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
            <span>24 Jam Lalu</span>
            <span>Real-time</span>
        </div>
    </div>

    <!-- Live Stream Sentinel Mission Log -->
    <div class="glass-panel rounded-3xl p-5 relative h-[500px] flex flex-col shrink-0 bg-slate-900/5">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4 shrink-0">
            <h2 class="text-[10px] font-black tracking-[0.2em] text-slate-400 flex items-center uppercase">
                <div class="w-6 h-6 rounded-lg bg-indigo-500 text-white flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                    <i class="fa-solid fa-bars-staggered text-[10px]"></i>
                </div>
                Sentinel Mission Log
            </h2>
            <div class="flex items-center space-x-2">
                <div class="text-[8px] font-bold text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded border border-indigo-500/20">STREAMING</div>
                <span class="flex h-1.5 w-1.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span>
                </span>
            </div>
        </div>
        
        <div class="bg-slate-900 rounded-2xl p-4 flex-1 overflow-hidden flex flex-col shadow-2xl border border-white/5">
            <div id="terminal-log" class="flex-1 overflow-y-auto custom-scrollbar font-mono-sentinel text-[10px] space-y-2 pr-2 leading-relaxed tracking-tight">
                <div class="text-blue-400/60">>> SENTINEL CORE INITIALIZED...</div>
                <div class="text-emerald-400/60">>> AWAITING SECURE TELEMETRY STREAM...</div>
            </div>
        </div>
    </div>
</div>
