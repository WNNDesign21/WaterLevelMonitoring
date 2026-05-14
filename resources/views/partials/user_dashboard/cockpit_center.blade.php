<!-- Column 2: The Core River Visual -->
<div class="lg:col-span-5 relative min-h-[400px] xl:min-h-[500px] flex items-stretch">
    <div class="v-reveal-bottom delay-3 glass-tank-container relative w-full h-full rounded-[2rem] shadow-[inset_0_0_50px_rgba(0,0,0,0.05)] bg-slate-100/50 border-4 border-white/60 overflow-hidden">
        
        <!-- Immersive Weather Overlay -->
        <div class="weather-rain" id="weather-rain">
            @for($i=0; $i<40; $i++)
                <div class="drop" style="left: {{ rand(1, 99) }}%; animation-duration: {{ 0.5 + (rand(0, 5) / 10) }}s; animation-delay: {{ rand(0, 10) / 10 }}s;"></div>
            @endfor
        </div>

        <!-- Accessibility Zone Patterns -->
        <div class="absolute inset-x-0 bottom-0 z-0 flex flex-col-reverse h-full pointer-events-none rounded-[2rem] overflow-hidden">
            <div class="pattern-overlay pattern-siaga1 h-[41.7%]" style="bottom: 58.3%"></div>
            <div class="pattern-overlay pattern-siaga2 h-[8.3%]" style="bottom: 50%"></div>
            <div class="pattern-overlay pattern-siaga3 h-[16.7%]" style="bottom: 33.3%"></div>
        </div>
        
        <!-- Water Layer -->
        <div class="liquid-water" id="river-water" style="height: 0%;">
            <div class="liquid-water-particles"></div>
            <div id="water-percent-container" class="absolute inset-x-0 top-1/2 -translate-y-1/2 text-center transition-all duration-700 pointer-events-none z-20">
                <div id="water-percent" class="text-6xl font-black text-white/20 uppercase tracking-tighter mix-blend-overlay transition-all duration-500">0%</div>
            </div>
        </div>

        <!-- Floating Surface Badge -->
        <div id="surface-badge" class="surface-badge !text-[10px] !px-3 !py-1" style="bottom: 0px; transform: translateY(50%);">
            <i id="surface-icon" class="fa-solid fa-circle-check"></i>
            <span id="surface-text">NORMAL</span>
        </div>

        <!-- Foreground Overlays -->
        <div class="absolute inset-0 z-40 pointer-events-none">
            @for($i = 0; $i <= 600; $i += 50)
                @php $bottomPercent = ($i / 600) * 100; $tmaValue = 8.00 + ($i / 100); @endphp
                @if($i % 100 == 0)
                    <div class="absolute right-0 left-0 h-px bg-slate-900/[0.08]" style="bottom: {{ $bottomPercent }}%"></div>
                    <div class="depth-tick !w-12 !bg-slate-900" style="bottom: {{ $bottomPercent }}%"></div>
                    <div class="depth-value !text-[10px]" style="bottom: {{ $bottomPercent }}%; transform: translateY(50%)">{{ number_format($tmaValue, 2) }}</div>
                @else
                    <div class="depth-tick !w-4" style="bottom: {{ $bottomPercent }}%"></div>
                @endif
            @endfor
        </div>
    </div>
</div>
