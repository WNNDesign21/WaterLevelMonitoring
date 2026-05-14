<!-- TOP BAR: Micro Metrics -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <div class="v-reveal-bottom delay-1 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-clock text-4xl"></i></div>
        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Waktu Aktif Server</span>
        <span class="text-sm font-mono text-emerald-500" id="it-uptime">99.98%</span>
    </div>
    <div class="v-reveal-bottom delay-2 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-network-wired text-4xl"></i></div>
        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Latensi Reverb</span>
        <span class="text-sm font-mono text-cyan-600"><span id="it-ping">12</span> ms</span>
    </div>
    <div class="v-reveal-bottom delay-3 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-bolt text-4xl"></i></div>
        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tegangan Node</span>
        <span class="text-sm font-mono text-amber-500" id="it-voltage">5.02 V</span>
    </div>
    <div class="v-reveal-bottom delay-4 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-users text-4xl"></i></div>
        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Koneksi Aktif</span>
        <span class="text-sm font-mono text-blue-500" id="it-connections">1,204</span>
    </div>
    <div class="v-reveal-bottom delay-5 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-memory text-4xl"></i></div>
        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Penggunaan Memori</span>
        <span class="text-sm font-mono text-slate-600" id="it-memory">42.8 GB / 64.0 GB</span>
    </div>
    <div class="v-reveal-bottom delay-6 bg-white border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-database text-4xl"></i></div>
        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Kueri DB/Detik</span>
        <span class="text-sm font-mono text-slate-600" id="it-qps">450</span>
    </div>
</div>
