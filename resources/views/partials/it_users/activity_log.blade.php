<!-- RIGHT: SYSTEM ACTIVITY LOG -->
<aside class="lg:col-span-3 space-y-6">
    <div class="v-reveal-right glass-panel p-6 rounded-[2.5rem] bg-white border border-slate-100 shadow-xl overflow-hidden" style="--delay: 1.2s">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">Access_Density</h3>
            <span class="text-[8px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">24H_HISTORY</span>
        </div>
        <div id="accessTrendChart" style="height: 140px;" class="w-full"></div>
    </div>

    <div class="v-reveal-right glass-panel p-6 rounded-[2.5rem] bg-white border border-slate-100 shadow-xl relative overflow-hidden h-full flex flex-col" style="--delay: 1.6s">
        <div class="absolute top-0 right-0 p-8 opacity-5"><i class="fa-solid fa-satellite-dish text-8xl"></i></div>
        <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em] mb-6 flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-3 animate-pulse"></span> Access_Event_Stream</h3>
        <div id="activity-log-stream" class="flex-1 space-y-6 overflow-y-auto pr-2 custom-scrollbar">
            <div class="flex items-center justify-center py-10"><div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div></div>
        </div>
        <div class="mt-8 pt-6 border-t border-slate-100">
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1">Infrastructure_Load</p>
                <div class="w-full bg-blue-200/50 h-1 rounded-full overflow-hidden mt-2"><div class="bg-blue-600 h-full w-[42%]"></div></div>
                <p class="text-[7px] text-blue-400 font-mono mt-2 uppercase tracking-tighter text-right">0.42_NODE_USAGE</p>
            </div>
            <a href="{{ route('it.users.history') }}" class="mt-4 w-full py-4 rounded-2xl border border-slate-200 bg-slate-50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-500 flex items-center justify-center group shadow-sm">
                <i class="fa-solid fa-clock-rotate-left mr-3 opacity-50 group-hover:rotate-[-45deg] transition-all"></i> VIEW_FULL_CHRONOLOGY
            </a>
        </div>
    </div>
</aside>
