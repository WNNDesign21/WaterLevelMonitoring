<!-- NEW: Interactive Geotagging Section -->
<div class="pt-8 space-y-6 pb-20">
    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center">
        <span class="mr-3">Konfigurasi Lokasi (Interactive Pin)</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </h3>
    
    <div class="glass-panel rounded-[2rem] p-6 space-y-4">
        <p class="text-xs text-slate-500 font-medium italic">Geser pin pada peta di bawah ini untuk menentukan lokasi persis perangkat di lapangan.</p>
        
        <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-inner group">
            <div id="interactive-map"></div>
            
            <!-- Save Tool -->
            <div class="absolute bottom-4 right-4 z-[1000]">
                <button id="btn-save-location" class="px-6 py-3 bg-blue-600 text-white text-xs font-black tracking-widest rounded-xl shadow-xl hover:bg-blue-700 transition-all flex items-center transform active:scale-95">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> SIMPAN LOKASI
                </button>
            </div>

            <!-- Coords Badge -->
            <div class="absolute top-4 left-4 z-[1000] pointer-events-none">
                <div class="bg-slate-900/80 backdrop-blur border border-white/10 p-3 rounded-xl shadow-2xl">
                    <div class="text-[8px] font-black text-blue-400 uppercase mb-1">Target Coordinates</div>
                    <div class="text-[10px] font-mono text-white/90" id="current-coords-display">{{ $device->latitude }}, {{ $device->longitude }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
