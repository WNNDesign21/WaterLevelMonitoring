<div class="lg:col-span-7 space-y-8">
    <div class="space-y-4">
        <div class="inline-block px-4 py-1.5 bg-blue-100 text-blue-600 text-[10px] font-black tracking-widest rounded-full uppercase">
            {{ $device->type }}
        </div>
        <h1 class="text-5xl font-black text-slate-800 tracking-tight leading-none">{{ $device->name }}</h1>
        <p class="text-xl text-slate-500 font-medium leading-relaxed">{{ $device->description }}</p>
        
        <!-- Address Detail HUD -->
        <div class="flex items-start space-x-3 bg-white/40 p-4 rounded-2xl border border-white/60 shadow-inner max-w-lg mt-2">
            <i class="fa-solid fa-location-dot text-blue-500 mt-1 animate-pulse"></i>
            <div>
                <div class="text-[8px] font-black text-blue-400 uppercase tracking-widest mb-1">Geotagged Address</div>
                <div id="device-address-display" class="text-[10px] font-bold text-slate-600 leading-tight">RESOLVING SENTINEL POSITION...</div>
            </div>
        </div>
    </div>

    <!-- Specs -->
    <div class="space-y-4">
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center">
            <span class="mr-3">Spesifikasi Teknis</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($device->specs as $key => $value)
            <div class="glass-panel rounded-2xl p-5 tech-card">
                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">{{ $key }}</div>
                <div class="text-base font-bold text-slate-800">{{ $value }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
