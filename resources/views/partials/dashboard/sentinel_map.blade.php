<div class="md:col-span-12 lg:col-span-3 flex flex-col">
    <!-- Sentinel GIS Map -->
    <div class="glass-panel rounded-3xl p-5 relative overflow-hidden h-[600px] flex flex-col">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4 px-1">
            <h2 class="text-[10px] font-black tracking-[0.2em] text-slate-400 border-b border-slate-100 pb-3 flex items-center uppercase flex-1">
                <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center mr-3 shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-satellite-dish text-[10px]"></i>
                </div>
                Sentinel GIS Data
            </h2>
            <div class="flex items-center space-x-1.5 px-2 py-1 bg-emerald-500/10 rounded-lg border border-emerald-500/20 shrink-0 h-fit self-start sm:self-center">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[8px] font-black text-emerald-500 uppercase tracking-tighter">Sentinel-2A Active</span>
            </div>
        </div>
        
        <div class="h-[calc(100%-80px)] rounded-[2rem] overflow-hidden border border-slate-200 relative group">
            <div id="sentinel-map" class="h-full w-full"></div>
            
            <div class="absolute bottom-6 left-6 z-[1000] pointer-events-none">
                <div class="bg-slate-900/90 backdrop-blur-xl border border-white/10 p-4 rounded-2xl shadow-2xl">
                    <div class="text-[8px] font-black text-blue-400 uppercase mb-1 tracking-widest">Active Sentinel Target</div>
                    <div class="text-xs font-mono-sentinel text-white font-bold" id="map-coords-badge">{{ $primaryDevice->latitude ?? '--' }}, {{ $primaryDevice->longitude ?? '--' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
