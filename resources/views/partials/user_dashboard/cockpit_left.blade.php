<!-- Column 1: Weather & Guides -->
<div class="lg:col-span-3 flex flex-col space-y-4">
    <div class="mb-4 flex flex-col space-y-4">
        <div>
            <h2 class="text-xl font-black text-slate-800 tracking-tight uppercase">Pantauan Publik</h2>
            <p class="text-slate-500 text-xs font-bold tracking-widest mt-1">SISTEM INFORMASI CITARUM</p>
        </div>
        
        <!-- LUXURIOUS DEVICE SELECTOR -->
        <div class="relative w-full z-40 group" id="device-selector-wrapper">
            <button type="button" onclick="toggleDeviceSelector()" id="device-selector-btn" class="w-full bg-white/80 backdrop-blur-md border border-slate-200/60 shadow-lg shadow-blue-900/5 hover:shadow-blue-900/10 rounded-2xl p-4 flex items-center justify-between transition-all duration-300">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shrink-0 shadow-inner">
                        <i class="fa-solid fa-satellite-dish text-white text-sm"></i>
                    </div>
                    <div class="text-left overflow-hidden">
                        <div class="text-[9px] uppercase font-black text-blue-500 tracking-widest mb-0.5">Lokasi Sensor Aktif</div>
                        <div class="text-sm font-bold text-slate-800 truncate" id="active-device-name">{{ $primaryDevice->name ?? 'Pilih Sensor' }}</div>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100 shrink-0 transition-transform duration-300" id="device-selector-icon">
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                </div>
            </button>
            
            <div id="device-selector-menu" class="absolute top-full left-0 w-full mt-2 bg-white/95 backdrop-blur-2xl border border-white/80 shadow-2xl rounded-2xl overflow-hidden transition-all duration-300 origin-top transform scale-y-0 opacity-0 invisible max-h-[350px] overflow-y-auto" style="scrollbar-width: thin;">
                <div class="p-2 space-y-1">
                    @if(isset($allDevices) && count($allDevices) > 0)
                        @foreach($allDevices as $device)
                        <button type="button" onclick="userSwitchDevice('{{ $device->slug }}', '{{ addslashes($device->name) }}', '{{ addslashes($device->location) }}', {{ $device->latitude ?? -6.2088 }}, {{ $device->longitude ?? 106.8456 }})" class="w-full text-left p-3 rounded-xl hover:bg-blue-50/80 transition-colors flex items-center space-x-3 group/item">
                            <div class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-red-500' }} shrink-0"></div>
                            <div class="flex-1 overflow-hidden">
                                <div class="text-xs font-bold text-slate-800 truncate group-hover/item:text-blue-600 transition-colors">{{ $device->name }}</div>
                                <div class="text-[9px] text-slate-400 uppercase tracking-wider truncate mt-0.5"><i class="fa-solid fa-location-crosshairs mr-1"></i> {{ $device->location ?: 'Lokasi Tidak Diketahui' }}</div>
                            </div>
                        </button>
                        @endforeach
                    @else
                        <div class="p-4 text-center text-xs text-slate-400">Tidak ada sensor tersedia</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Weather -->
    <div class="v-reveal-left delay-1 rounded-3xl p-5 relative overflow-hidden bg-gradient-to-br from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-500/30 group">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all"></div>
        <div class="relative z-10">
            <div class="flex items-center space-x-2 mb-3">
                <i class="fa-solid fa-location-dot text-cyan-200 animate-bounce text-sm"></i>
                <span id="sky-location-detail" class="text-[10px] font-black uppercase tracking-widest truncate">Lokasi...</span>
            </div>
            <div class="flex items-center space-x-4 mb-4">
                <div id="sky-icon-main" class="text-5xl filter drop-shadow-md"><i class="fa-solid fa-cloud-sun text-amber-300"></i></div>
                <div>
                    <div id="sky-temp" class="text-4xl font-black tracking-tighter leading-none skeleton w-24 h-10 mb-1"></div>
                    <div id="sky-desc" class="text-[10px] font-bold text-blue-100 uppercase tracking-widest mt-1 skeleton w-32 h-3"></div>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-white/20">
                <div class="text-center"><div class="text-[9px] uppercase font-black text-blue-200 mb-1">Angin</div><div id="sky-wind" class="font-bold text-xs">--</div></div>
                <div class="w-px h-6 bg-white/20"></div>
                <div class="text-center"><div class="text-[9px] uppercase font-black text-blue-200 mb-1">Lembap</div><div id="sky-humidity" class="font-bold text-xs">--</div></div>
            </div>
        </div>
    </div>

    <!-- Compact Status Guide -->
    <div class="v-reveal-left delay-2 rounded-3xl p-5 bg-white border border-slate-100 shadow-sm flex-1">
        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4 flex items-center"><i class="fa-solid fa-circle-info mr-2"></i> Panduan</h3>
        <div class="space-y-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-xs"></i></div>
                <div><div class="text-[10px] font-black uppercase text-slate-700 leading-none">Normal</div><div class="text-[9px] text-slate-400 mt-1">Aman</div></div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation text-xs"></i></div>
                <div><div class="text-[10px] font-black uppercase text-slate-700 leading-none">Siaga 3</div><div class="text-[9px] text-slate-400 mt-1">Waspada</div></div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-bell text-xs"></i></div>
                <div><div class="text-[10px] font-black uppercase text-slate-700 leading-none">Siaga 2</div><div class="text-[9px] text-slate-400 mt-1">Siaga Banjir</div></div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0 animate-pulse"><i class="fa-solid fa-skull-crossbones text-xs"></i></div>
                <div><div class="text-[10px] font-black uppercase text-red-600 leading-none">Siaga 1</div><div class="text-[9px] text-red-500 font-bold mt-1">Evakuasi Kritis!</div></div>
            </div>
        </div>
    </div>
</div>
