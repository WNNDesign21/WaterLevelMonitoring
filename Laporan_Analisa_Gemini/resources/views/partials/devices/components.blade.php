@if($device->components)
<div class="space-y-6 pt-4">
    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center">
        <span class="mr-3">Breakdown Komponen</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($device->components as $comp)
        <div class="glass-panel rounded-3xl p-6 flex flex-col space-y-4 relative overflow-hidden group border-l-4 @if($comp['id'] == 'comp-mega') border-blue-500 @else border-cyan-500 @endif">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/' . $comp['image']) }}" alt="{{ $comp['name'] }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $comp['type'] }}</div>
                        <div class="text-sm font-black text-slate-800 tracking-tight">{{ $comp['name'] }}</div>
                    </div>
                </div>
                <div class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[8px] font-black tracking-widest rounded-md border border-emerald-100 uppercase">Active</div>
            </div>
            
            <!-- Real-time Metrics for Component -->
            <div class="grid grid-cols-3 gap-2">
                @foreach($comp['metrics'] as $mKey => $mVal)
                <div class="bg-slate-50/50 rounded-xl p-2 border border-slate-100/50">
                    <div class="text-[8px] font-bold text-slate-400 uppercase mb-0.5">{{ str_replace('_', ' ', $mKey) }}</div>
                    <div class="text-[11px] font-black text-slate-700 tracking-tighter" id="metric-{{ $comp['id'] }}-{{ $mKey }}">{{ $mVal }}</div>
                </div>
                @endforeach
            </div>

            <!-- Individual Specs -->
            <div class="pt-2 flex flex-wrap gap-2">
                @foreach($comp['specs'] as $sKey => $sVal)
                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-bold rounded-md">{{ $sVal }}</span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Identification -->
<div class="glass-panel rounded-3xl p-8 flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0 shadow-sm border-dashed my-8">
    <div class="flex items-center space-x-6">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
            <i class="fa-solid fa-microchip text-2xl"></i>
        </div>
        <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">Nomor Seri (UUID)</div>
            <div class="text-lg font-mono font-bold text-slate-700 tracking-wider">{{ $device->serial_number }}</div>
        </div>
    </div>
    <div class="text-right">
        <div class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">Terakhir Aktif</div>
        <div class="text-sm font-bold text-slate-600" id="last-seen-text">{{ $device->last_seen ? $device->last_seen->diffForHumans() : 'N/A' }}</div>
    </div>
</div>
