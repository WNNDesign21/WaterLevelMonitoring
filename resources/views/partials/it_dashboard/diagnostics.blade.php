<!-- LEFT COLUMN: System Diagnostics -->
<div class="lg:col-span-3 flex flex-col space-y-5">
    <!-- Status Perangkat Keras -->
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
