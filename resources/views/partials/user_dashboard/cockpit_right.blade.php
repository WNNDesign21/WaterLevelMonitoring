<!-- Column 3: Telemetry & Alerts -->
<div class="lg:col-span-4 flex flex-col justify-center space-y-4">
    <!-- Alert Banner -->
    <div id="alert-banner" class="v-reveal-right delay-4 px-5 py-3 rounded-2xl flex items-center space-x-3 transition-all duration-300 bg-emerald-50 text-emerald-600 border border-emerald-100 hidden">
        <i id="alert-icon" class="fa-solid fa-shield-check text-3xl"></i>
        <div>
            <div class="text-[10px] font-black tracking-widest uppercase mb-1">Status Sungai</div>
            <div id="alert-message" class="font-black text-xl tracking-wide uppercase leading-none">Aman Terkendali</div>
        </div>
    </div>

    <!-- Main KPI & Map Row -->
    <div class="grid grid-cols-2 gap-4 flex-1">
        <div class="v-reveal-right delay-5 rounded-[2rem] p-6 bg-white border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-colors flex flex-col justify-center">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-water text-8xl"></i></div>
            <h3 class="text-[10px] text-slate-400 font-black mb-3 uppercase tracking-[0.2em] relative z-10">Tinggi Permukaan Air</h3>
            <div class="flex items-baseline mb-4 relative z-10">
                <div id="water-level" class="text-5xl xl:text-6xl font-black text-slate-800 odometer tracking-tighter leading-none font-rajdhani skeleton w-32 h-14"></div>
                <span class="text-lg text-blue-500 font-bold ml-2">MDPL</span>
            </div>
            <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50 inline-flex px-3 py-1.5 rounded-lg border border-slate-100 relative z-10">
                <i class="fa-solid fa-clock text-blue-500"></i><span id="live-clock">--:--:-- WIB</span>
            </div>
            <div class="mt-4 h-16 w-full relative z-10 opacity-80"><canvas id="tma-sparkline"></canvas></div>
        </div>

        <div class="v-reveal-right delay-6 rounded-[2rem] p-2 bg-white border border-slate-100 shadow-xl overflow-hidden relative flex flex-col">
            <div class="absolute top-4 left-4 z-[400] flex items-center bg-white/80 backdrop-blur-sm px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2 animate-ping" id="map-radar-dot"></span>
                <span class="text-[9px] font-black uppercase text-slate-600 tracking-widest">Satelit Aktif</span>
            </div>
            <div id="sentinel-map" class="w-full flex-1 min-h-[150px] rounded-[1.5rem] z-0 filter contrast-100 saturate-100"></div>
        </div>
    </div>

    <!-- Advanced Metrics Grid -->
    <div class="grid grid-cols-2 gap-4">
        <div class="v-reveal-bottom delay-7 rounded-3xl p-5 bg-white border border-slate-100 shadow-sm flex flex-col justify-center">
            <h3 class="text-[9px] text-slate-400 font-black mb-1 uppercase tracking-[0.2em] leading-tight">Jarak Sensor</h3>
            <div class="flex items-baseline">
                <span id="current-distance" class="text-3xl font-bold text-slate-700 odometer font-rajdhani skeleton w-16 h-8"></span>
                <span class="text-xs text-slate-400 font-bold ml-1">cm</span>
            </div>
        </div>
        
        <div class="v-reveal-bottom delay-8 rounded-3xl p-5 bg-white border border-slate-100 shadow-sm flex flex-col justify-center">
            <h3 class="text-[9px] text-slate-400 font-black mb-1 uppercase tracking-[0.2em] leading-tight">Laju Air</h3>
            <div class="flex items-baseline text-blue-500 transition-colors" id="velocity-container">
                <i class="fa-solid fa-arrow-right text-xs mr-1" id="velocity-icon"></i>
                <span id="flow-velocity" class="text-2xl font-bold odometer font-rajdhani">0.0</span>
                <span class="text-xs font-bold ml-1">cm/s</span>
            </div>
        </div>

        <div class="v-reveal-bottom delay-8 col-span-2 rounded-3xl p-5 bg-white border border-slate-100 shadow-xl flex items-center justify-between relative overflow-hidden group" id="eta-card">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-microchip text-6xl text-blue-500"></i></div>
            <div class="relative z-10">
                <h3 class="text-[9px] text-blue-500 font-black mb-1 uppercase tracking-[0.2em]" id="eta-label">AI Prediksi (ETA Meluap)</h3>
                <div class="text-xl font-bold text-slate-800 font-rajdhani" id="eta-overflow">Mengkalkulasi...</div>
            </div>
            <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100 relative z-10" id="eta-icon-wrapper"><i class="fa-solid fa-shield-check text-blue-500" id="eta-icon"></i></div>
        </div>
    </div>
</div>
