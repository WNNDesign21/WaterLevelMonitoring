<!-- RIGHT COLUMN: Weather, Map, AI Predictions -->
<div class="lg:col-span-3 flex flex-col space-y-5">
    <!-- Weather Metric -->
    <div class="v-reveal-bottom delay-2 bg-white border border-slate-100 rounded-[1.5rem] p-4 relative overflow-hidden shadow-lg mb-5 group">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center">
                <i class="fa-solid fa-cloud-sun mr-2 text-blue-500"></i> Kondisi Cuaca Satelit
            </h3>
            <span id="sky-location-detail" class="text-[8px] font-mono text-slate-400 uppercase">GPS_SIGNAL_OK</span>
        </div>
        <div class="flex items-center space-x-4">
            <div id="sky-icon-main" class="text-4xl filter drop-shadow-sm">
                <i class="fa-solid fa-cloud text-slate-300"></i>
            </div>
            <div>
                <div id="sky-temp" class="text-3xl font-black text-slate-800 font-mono tracking-tighter skeleton w-20 h-10 mb-1"></div>
                <div id="sky-desc" class="text-[9px] font-bold text-cyan-600 uppercase tracking-widest mt-1 skeleton w-24 h-3"></div>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-slate-50">
            <div><div class="text-[7px] uppercase font-black text-slate-400 mb-0.5">Humidity</div><div id="sky-humidity" class="text-[10px] font-mono font-bold text-slate-700">--</div></div>
            <div><div class="text-[7px] uppercase font-black text-slate-400 mb-0.5">Pressure</div><div id="sky-pressure" class="text-[10px] font-mono font-bold text-slate-700">--</div></div>
            <div><div class="text-[7px] uppercase font-black text-slate-400 mb-0.5">Wind</div><div id="sky-wind" class="text-[10px] font-mono font-bold text-slate-700">--</div></div>
        </div>
    </div>

    <!-- IT Sentinel Map -->
    <div class="v-reveal-bottom delay-3 bg-white border border-slate-100 rounded-[1.5rem] p-2 relative h-[250px] flex flex-col shadow-lg overflow-hidden">
        <div class="absolute top-4 left-4 z-[400] bg-white/90 backdrop-blur-sm border border-slate-200 px-3 py-1.5 rounded-lg flex items-center space-x-2 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
            <span class="text-[9px] font-mono text-slate-600 uppercase tracking-widest">UPLINK_SATELIT</span>
        </div>
        <div id="sentinel-map" class="w-full flex-1 rounded-[1rem] z-0 bg-slate-100"></div>
    </div>

    <!-- AI ETA Predictions -->
    <div class="v-reveal-bottom delay-5 bg-white border border-slate-100 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500" id="it-eta-card">
        <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-brain text-9xl text-slate-300"></i></div>
        <h3 class="text-[9px] text-slate-500 font-black mb-5 uppercase tracking-[0.2em] flex items-center border-b border-slate-100 pb-2">
            <i class="fa-solid fa-clock-rotate-left mr-2 text-blue-500"></i> Matriks AI Prediktif
        </h3>
        <div class="space-y-6 relative z-10">
            <div>
                <span class="text-[8px] font-mono text-slate-400 uppercase block mb-1">Estimasi Waktu Meluap</span>
                <div class="text-3xl font-black text-emerald-500 font-mono" id="eta-overflow">STABLE</div>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <span class="text-[8px] font-mono text-slate-400 uppercase block mb-2">Tingkat Kepercayaan</span>
                <div class="flex items-center space-x-3">
                    <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200"><div class="bg-gradient-to-r from-blue-500 to-blue-400 h-2 rounded-full" style="width: 94%"></div></div>
                    <span class="text-[10px] font-mono text-blue-500 font-bold">94.2%</span>
                </div>
            </div>
            <div class="pt-2">
                <span class="text-[8px] font-mono text-slate-400 uppercase block mb-2">Model Regresi</span>
                <div class="text-[9px] font-mono text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-200">
                    Y = &beta;₀ + &beta;₁X + &epsilon;<br><span class="text-slate-400">Node Pemrosesan:</span> GPU_04<br><span class="text-slate-400">Status:</span> <span class="text-emerald-500">OPTIMAL</span>
                </div>
            </div>
        </div>
    </div>
</div>
