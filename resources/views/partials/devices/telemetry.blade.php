<div class="space-y-6 pt-4">
    <h3 class="text-xs font-black text-blue-500 uppercase tracking-widest flex items-center">
        <span class="mr-3">Telemetri Real-time</span>
        <div class="flex-1 h-px bg-blue-100"></div>
        <span class="ml-3 flex h-2 w-2 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
        </span>
    </h3>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="glass-panel rounded-2xl p-4 border-b-2 border-cyan-400">
            <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Arus (mA)</div>
            <div class="text-2xl font-black text-slate-800 tracking-tighter" id="rt-current">0.0</div>
        </div>
        <div class="glass-panel rounded-2xl p-4 border-b-2 border-indigo-400">
            <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Tegangan (V)</div>
            <div class="text-2xl font-black text-slate-800 tracking-tighter" id="rt-voltage">5.00</div>
        </div>
        <div class="glass-panel rounded-2xl p-4 border-b-2 border-violet-400">
            <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Latensi (ms)</div>
            <div class="text-2xl font-black text-slate-800 tracking-tighter" id="rt-latency">--</div>
        </div>
        <div class="glass-panel rounded-2xl p-4 border-b-2 border-emerald-400">
            <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Data Flow</div>
            <div class="text-2xl font-black text-slate-800 tracking-tighter" id="rt-flow">--</div>
        </div>
    </div>

    <!-- Oscilloscope Chart for Current -->
    <div class="glass-panel rounded-3xl p-6 h-40 relative overflow-hidden bg-slate-900 shadow-2xl shadow-blue-500/10">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: linear-gradient(#334155 1px, transparent 1px), linear-gradient(90deg, #334155 1px, transparent 1px); background-size: 20px 20px;"></div>
        <div class="absolute top-4 left-6 text-[9px] font-bold text-blue-400 uppercase tracking-widest z-10">OSCILLOSCOPE: CURRENT_ANALYTICS</div>
        <canvas id="telemetryCanvas" class="w-full h-full relative z-0"></canvas>
    </div>
</div>
