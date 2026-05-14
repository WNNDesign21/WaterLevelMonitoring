<!-- OVERPOWERED SECURITY METRICS -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 v-reveal-bottom" style="--delay: 0.6s">
    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-blue-500 transition-all">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600"><i class="fa-solid fa-shield-halved text-xl"></i></div>
            <div>
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Auth_Status</h4>
                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">INTEGRITY_SAFE</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-[8px] font-mono text-emerald-500 font-bold">100.0%</div>
            <div class="w-12 h-1 bg-emerald-100 rounded-full overflow-hidden mt-1"><div class="bg-emerald-500 h-full w-full"></div></div>
        </div>
    </div>

    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-cyan-500 transition-all">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600"><i class="fa-solid fa-users text-xl"></i></div>
            <div>
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active_Admins</h4>
                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">02_OPERATIONAL</div>
            </div>
        </div>
        <div class="relative"><div class="w-2 h-2 rounded-full bg-cyan-500 animate-ping"></div></div>
    </div>

    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-amber-500 transition-all">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600"><i class="fa-solid fa-satellite-dish text-xl"></i></div>
            <div>
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System_Uplink</h4>
                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">ONLINE</div>
            </div>
        </div>
        <div class="text-[9px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">SYNCED</div>
    </div>

    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-blue-500 transition-all">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-slate-900 flex items-center justify-center text-white"><i class="fa-solid fa-fingerprint text-xl"></i></div>
            <div>
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Last_Sync</h4>
                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">{{ now()->format('H:i') }}_WIB</div>
            </div>
        </div>
        <i class="fa-solid fa-chevron-right text-slate-200 text-xs"></i>
    </div>
</div>
