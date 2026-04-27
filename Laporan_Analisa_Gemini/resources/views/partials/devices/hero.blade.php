<div class="lg:col-span-5 space-y-8">
    <div class="glass-panel rounded-[3rem] p-10 flex items-center justify-center relative overflow-hidden group">
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-blue-100 rounded-full blur-3xl opacity-30"></div>
        <img src="{{ asset('images/' . $device->image_path) }}" alt="{{ $device->name }}" class="relative z-10 w-full object-contain drop-shadow-2xl group-hover:scale-105 transition-transform duration-700">
    </div>

    <div class="glass-panel rounded-3xl p-8 space-y-6">
        <div>
            <div class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-1">Status Operational</div>
            <div class="flex items-center space-x-3">
                <div id="rt-status-dot" class="w-3 h-3 rounded-full @if($device->status == 'online') bg-emerald-500 @elseif($device->status == 'maintenance') bg-amber-500 @else bg-slate-400 @endif"></div>
                <span id="rt-status-text" class="text-xl font-bold text-slate-800 uppercase tracking-tight">{{ $device->status }}</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Uptime</div>
                <div class="text-lg font-bold text-slate-800">99.8%</div>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Sinyal</div>
                <div class="text-lg font-bold text-emerald-500">EXCELLENT</div>
            </div>
        </div>
    </div>
</div>
