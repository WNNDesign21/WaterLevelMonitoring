<!-- WATER LEVEL HISTORY MATRIX -->
<div class="v-reveal-bottom delay-8 mt-6">
    <div class="glass-panel rounded-[2.5rem] p-6 lg:p-8 bg-white/60 shadow-2xl backdrop-blur-2xl border border-white/80 overflow-hidden relative">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8 relative z-10">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase flex items-center">
                    <i class="fa-solid fa-chart-line mr-3 text-blue-500"></i> Riwayat Elevasi <span class="text-blue-500 ml-2" id="history-device-name">{{ $primaryDevice->name ?? 'Citarum' }}</span>
                </h2>
                <p class="text-slate-500 text-xs font-bold tracking-widest mt-1">ANALISIS TREN HISTORIS PER JAM</p>
            </div>

            <!-- Range Selectors -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="bg-slate-100/80 p-1.5 rounded-2xl flex items-center space-x-1 border border-slate-200/50 shadow-inner">
                    <button onclick="updateHistoryRange('daily', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 bg-white text-blue-600 shadow-sm border border-blue-100">Harian</button>
                    <button onclick="updateHistoryRange('weekly', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 text-slate-500 hover:text-blue-500">Mingguan</button>
                    <button onclick="updateHistoryRange('monthly', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 text-slate-500 hover:text-blue-500">Bulanan</button>
                    <button onclick="updateHistoryRange('yearly', this)" class="history-range-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 text-slate-500 hover:text-blue-500">Tahunan</button>
                </div>
                
                <div class="flex items-center space-x-2 bg-white/80 border border-slate-200 rounded-2xl p-1 shadow-sm">
                    <input type="date" id="history-start-date" class="bg-transparent border-none text-[10px] font-bold text-slate-600 focus:ring-0 p-1">
                    <span class="text-slate-300 text-[10px] font-bold">SD</span>
                    <input type="date" id="history-end-date" class="bg-transparent border-none text-[10px] font-bold text-slate-600 focus:ring-0 p-1">
                    <button onclick="updateHistoryRange('custom', this)" class="bg-blue-500 text-white p-2 rounded-xl hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="relative h-[350px] lg:h-[450px] w-full bg-slate-50/50 rounded-3xl p-4 border border-slate-100 shadow-inner group">
            <div id="history-chart-loading" class="absolute inset-0 z-20 flex items-center justify-center bg-white/40 backdrop-blur-sm hidden">
                <div class="flex flex-col items-center"><div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div><span class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-4">Sinkronisasi Data...</span></div>
            </div>
            <canvas id="historyMainChart"></canvas>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/80 border border-slate-100 p-4 rounded-[1.5rem] shadow-sm"><div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Rata-rata TMA</div><div class="text-xl font-bold text-slate-700" id="hist-avg-tma">-- m</div></div>
            <div class="bg-white/80 border border-slate-100 p-4 rounded-[1.5rem] shadow-sm border-l-4 border-l-blue-500"><div class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-1">Puncak Tertinggi</div><div class="text-xl font-bold text-slate-700" id="hist-max-tma">-- m</div></div>
            <div class="bg-white/80 border border-slate-100 p-4 rounded-[1.5rem] shadow-sm border-l-4 border-l-cyan-500"><div class="text-[9px] font-black text-cyan-500 uppercase tracking-widest mb-1">Level Terendah</div><div class="text-xl font-bold text-slate-700" id="hist-min-tma">-- m</div></div>
            <div class="bg-white/80 border border-slate-100 p-4 rounded-[1.5rem] shadow-sm"><div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Sampel Jam</div><div class="text-xl font-bold text-slate-700" id="hist-sample-count">--</div></div>
        </div>
    </div>
</div>
