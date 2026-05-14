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

        <div class="relative h-[400px] w-full bg-slate-50/50 rounded-3xl p-6 border border-slate-100 shadow-inner group overflow-hidden">
            <div id="history-chart-loading" class="absolute inset-0 z-20 flex items-center justify-center bg-white/40 backdrop-blur-sm hidden">
                <div class="flex flex-col items-center"><div class="w-12 h-12 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin"></div><span class="text-[9px] font-mono text-cyan-600 uppercase tracking-widest mt-4">FETCHING_HISTORICAL_DATA...</span></div>
            </div>
            <canvas id="historyMainChart"></canvas>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl"><div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">AVG_TMA_MDPL</div><div class="text-2xl font-black text-slate-700 font-mono" id="hist-avg-tma">--</div></div>
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl border-l-4 border-l-emerald-500"><div class="text-[8px] font-black text-emerald-600 uppercase tracking-widest mb-1">MAX_PEAK_RECORDS</div><div class="text-2xl font-black text-slate-700 font-mono" id="hist-max-tma">--</div></div>
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl border-l-4 border-l-blue-500"><div class="text-[8px] font-black text-blue-600 uppercase tracking-widest mb-1">MIN_BASE_RECORDS</div><div class="text-2xl font-black text-slate-700 font-mono" id="hist-min-tma">--</div></div>
            <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl"><div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">SAMPLE_DATA_POINTS</div><div class="text-2xl font-black text-slate-700 font-mono" id="hist-sample-count">--</div></div>
        </div>
    </div>
</div>
