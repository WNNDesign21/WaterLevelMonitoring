@include('partials.dashboard.head')

<style>
    /* DIRECT CACHE BYPASS CSS - NOC ASSEMBLY SYSTEM */
    @keyframes revealFromLeft { 0% { opacity: 0; transform: translateX(-100px); filter: blur(20px); } 100% { opacity: 1; transform: translateX(0); filter: blur(0); } }
    @keyframes revealFromRight { 0% { opacity: 0; transform: translateX(100px); filter: blur(20px); } 100% { opacity: 1; transform: translateX(0); filter: blur(0); } }
    @keyframes revealFromBottom { 0% { opacity: 0; transform: translateY(100px); filter: blur(20px); } 100% { opacity: 1; transform: translateY(0); filter: blur(0); } }
    @keyframes revealFromTop { 0% { opacity: 0; transform: translateY(-100px); filter: blur(20px); } 100% { opacity: 1; transform: translateY(0); filter: blur(0); } }

    .v-reveal-left, .v-reveal-right, .v-reveal-bottom, .v-reveal-top {
        opacity: 0; animation-duration: 1.8s; animation-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1); animation-fill-mode: forwards; animation-delay: var(--delay, 0s);
    }

    body.loaded .v-reveal-left { animation-name: revealFromLeft; }
    body.loaded .v-reveal-right { animation-name: revealFromRight; }
    body.loaded .v-reveal-bottom { animation-name: revealFromBottom; }
    body.loaded .v-reveal-top { animation-name: revealFromTop; }

    .v-reveal-item { opacity: 0; }
    @keyframes revealFadeUp { 0% { opacity: 0; transform: translateY(30px); filter: blur(10px); } 100% { opacity: 1; transform: translateY(0); filter: blur(0); } }
    body.loaded .v-reveal-item { animation: revealFadeUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

    body.loaded .delay-1 { animation-delay: 0.2s !important; }
    body.loaded .delay-2 { animation-delay: 0.6s !important; }
    body.loaded .delay-3 { animation-delay: 1.0s !important; }
    body.loaded .delay-4 { animation-delay: 1.4s !important; }
    body.loaded .delay-5 { animation-delay: 1.8s !important; }
    body.loaded .delay-6 { animation-delay: 2.2s !important; }
    body.loaded .delay-7 { animation-delay: 2.6s !important; }
    body.loaded .delay-8 { animation-delay: 3.0s !important; }
    body.loaded .delay-9 { animation-delay: 3.4s !important; }
</style>

<body class="min-h-screen bg-slate-50 text-slate-700 font-sans selection:bg-cyan-200 selection:text-cyan-900 pb-10 overflow-x-hidden">
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-500 z-50 shadow-[0_0_10px_rgba(6,182,212,0.3)]"></div>

    <div class="w-full max-w-[2000px] mx-auto px-4 sm:px-6 lg:px-8 mt-6 relative z-10">
        
        <!-- HEADER -->
        <div class="mb-8">
            <div class="glass-panel p-4 md:p-6 rounded-[2rem] bg-white/60 border border-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-6 v-reveal-top" style="--delay: 0.2s">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 flex items-center justify-center shrink-0">
                        <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" class="w-12 h-12 object-contain drop-shadow-lg">
                    </div>
                    <div class="border-r border-slate-200 pr-6 mr-2 hidden md:block">
                        <h1 class="text-xl font-black text-slate-800 tracking-tighter uppercase leading-none">Water<span class="text-cyan-600">Sense</span></h1>
                        <p class="text-[9px] font-mono text-cyan-600 tracking-[0.3em] uppercase mt-1">IT_COMMAND_CENTER</p>
                    </div>
                    <div class="hidden xl:flex flex-col">
                        <span id="header-time" class="text-xs font-black text-slate-700 font-mono tracking-widest">00:00:00</span>
                        <span id="header-date" class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-1">WIB_KARAWANG_SECTOR</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div id="global-connectivity-badge" class="flex items-center space-x-3 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl shadow-sm transition-all duration-500">
                        <div class="relative"><span id="global-status-dot" class="w-2.5 h-2.5 rounded-full bg-emerald-500 block"></span><span id="global-status-ping" class="absolute inset-0 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping opacity-75"></span></div>
                        <span id="global-status-text" class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">SENTINEL_ONLINE</span>
                    </div>

                    <div class="flex items-center bg-slate-100/50 p-1 rounded-2xl border border-slate-200/30">
                        <a href="{{ route('it.users.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all flex items-center"><i class="fa-solid fa-users-gear mr-2 text-[12px]"></i> Users</a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.analytics.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-blue-600 hover:text-white transition-all flex items-center"><i class="fa-solid fa-chart-line mr-2 text-[12px]"></i> Analytics</a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.devices.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-cyan-500 hover:text-white transition-all flex items-center"><i class="fa-solid fa-gears mr-2 text-[12px]"></i> Device</a>
                    </div>

                    <a href="{{ route('user.dashboard') }}" target="_blank" class="flex items-center space-x-3 bg-gradient-to-r from-slate-800 to-slate-900 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] hover:scale-105 active:scale-95 transition-all shadow-xl shadow-slate-200 group border border-slate-700">
                        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span></span>
                        <span>Lihat Live</span><i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-500 group-hover:text-white transition-colors"></i>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-[11px] font-black text-slate-800 uppercase tracking-tighter leading-none">{{ auth()->user()->name }}</div>
                        <div class="text-[8px] font-bold text-cyan-600 uppercase tracking-widest mt-1.5 flex items-center justify-end"><span class="w-1 h-1 rounded-full bg-cyan-500 mr-1.5"></span> IT_ADMIN_RANK</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0f172a&color=fff' }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-lg">
                        <form action="{{ route('logout') }}" method="POST" class="inline">@csrf<button type="submit" class="w-10 h-10 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 flex items-center justify-center group"><i class="fa-solid fa-power-off text-[12px] group-hover:scale-110 transition-transform"></i></button></form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function updateHeaderClock() {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const element = document.getElementById('header-time');
                if(element) element.textContent = timeStr.replace(/\./g, ':');
            }
            setInterval(updateHeaderClock, 1000); updateHeaderClock();
        </script>

        @include('partials.it_dashboard.metrics')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            @include('partials.it_dashboard.diagnostics')
            @include('partials.it_dashboard.telemetry_kpi')
            @include('partials.it_dashboard.sidebar_metrics')
        </div>

        @include('partials.it_dashboard.history')
    </div>

    @include('partials.dashboard.calibration_modal')
    @include('partials.dashboard.scripts')
    @include('partials.it_dashboard.it_scripts')
    @include('partials.dashboard.history_scripts')
</body>
</html>
