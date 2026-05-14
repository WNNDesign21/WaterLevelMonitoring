@include('partials.dashboard.head')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<style>
    /* JARVIS CINEMATIC ENTRY PROTOCOL */
    @keyframes revealFromLeft { 0% { opacity: 0; transform: translateX(-100px); filter: blur(20px); } 100% { opacity: 1; transform: translateX(0); filter: blur(0); } }
    @keyframes revealFromRight { 0% { opacity: 0; transform: translateX(100px); filter: blur(20px); } 100% { opacity: 1; transform: translateX(0); filter: blur(0); } }
    @keyframes revealFromBottom { 0% { opacity: 0; transform: translateY(100px); filter: blur(20px); } 100% { opacity: 1; transform: translateY(0); filter: blur(0); } }
    @keyframes revealFromTop { 0% { opacity: 0; transform: translateY(-100px); filter: blur(20px); } 100% { opacity: 1; transform: translateY(0); filter: blur(0); } }
    @keyframes revealScale { 0% { opacity: 0; transform: scale(0.9); filter: blur(20px); } 100% { opacity: 1; transform: scale(1); filter: blur(0); } }

    .v-reveal-left, .v-reveal-right, .v-reveal-bottom, .v-reveal-top, .v-reveal-scale {
        opacity: 0; animation-duration: 1.8s; animation-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1); animation-fill-mode: forwards; animation-delay: var(--delay, 0s);
    }

    body.loaded .v-reveal-left { animation-name: revealFromLeft; }
    body.loaded .v-reveal-right { animation-name: revealFromRight; }
    body.loaded .v-reveal-bottom { animation-name: revealFromBottom; }
    body.loaded .v-reveal-top { animation-name: revealFromTop; }
    body.loaded .v-reveal-scale { animation-name: revealScale; }

    @keyframes fade-in { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }
    .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
</style>

<body class="min-h-screen bg-slate-50 font-sans antialiased selection:bg-blue-200 selection:text-blue-900 relative pb-10">
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-500 z-50 shadow-[0_0_10px_rgba(59,130,246,0.3)]"></div>

    <div class="max-w-[2000px] mx-auto px-6 mt-6">
        <!-- HEADER -->
        <div class="mb-10">
            <div class="glass-panel p-4 md:p-5 rounded-[2rem] bg-white/60 border border-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-6 v-reveal-top" style="--delay: 0.2s">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('it.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-slate-900 text-white rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-slate-900/20 group"><i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i></a>
                    <div class="flex items-center space-x-3 border-r border-slate-200 pr-6 mr-2 hidden md:flex"><img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" class="w-10 h-10 object-contain drop-shadow-md"><div><h1 class="text-lg font-black text-slate-800 tracking-tighter uppercase leading-none">Water<span class="text-cyan-600">Sense</span></h1><p class="text-[8px] font-mono text-cyan-600 tracking-[0.3em] uppercase mt-1">USER_HQ</p></div></div>
                    <div class="hidden xl:flex flex-col"><span id="header-time" class="text-xs font-black text-slate-700 font-mono tracking-widest">00:00:00</span><span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-1">WIB_KARAWANG_SECTOR</span></div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div class="flex items-center space-x-3 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl shadow-sm"><div class="relative"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 block"></span><span class="absolute inset-0 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping opacity-75"></span></div><span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">USER_MANAGEMENT_MODE</span></div>
                    <div class="flex items-center bg-slate-100/50 p-1 rounded-2xl border border-slate-200/30">
                        <a href="{{ route('it.dashboard') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all">Dashboard</a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.analytics.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-blue-600 hover:text-white transition-all">Analytics</a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.devices.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-cyan-500 hover:text-white transition-all">Device</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block"><div class="text-[11px] font-black text-slate-800 uppercase tracking-tighter leading-none">{{ auth()->user()->name }}</div><div class="text-[8px] font-bold text-cyan-600 uppercase tracking-widest mt-1.5 flex items-center justify-end">IT_ADMIN_RANK</div></div>
                    <div class="flex items-center space-x-2">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0f172a&color=fff' }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-lg">
                        <form action="{{ route('logout') }}" method="POST" class="inline">@csrf<button type="submit" class="w-10 h-10 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all shadow-lg flex items-center justify-center"><i class="fa-solid fa-power-off text-xs"></i></button></form>
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

        <main class="space-y-10">
            <!-- Session Messages -->
            @if(session('success_password'))
            <div class="glass-panel p-8 rounded-[2rem] bg-blue-600/10 border border-blue-600/20 v-reveal-scale relative overflow-hidden" style="--delay: 0.8s">
                <div class="absolute top-0 right-0 p-8 opacity-10"><i class="fa-solid fa-key text-6xl text-blue-600"></i></div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
                    <div><div class="flex items-center space-x-2 mb-2"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span><span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">User Berhasil Didaftarkan</span></div><h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Kredensial Akses <span class="text-blue-600">{{ session('success_password')['name'] }}</span></h3><p class="text-[10px] font-bold text-slate-500 mt-2 max-w-md">Harap simpan password ini. Password hanya akan ditampilkan sekali demi keamanan.</p></div>
                    <div class="flex items-center space-x-4"><div class="bg-white px-6 py-4 rounded-2xl border border-slate-200 shadow-sm text-center"><div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Temporary Password</div><div class="text-xl font-black text-blue-600 font-mono tracking-wider" id="tempPass">{{ session('success_password')['password'] }}</div></div><button onclick="navigator.clipboard.writeText(document.getElementById('tempPass').innerText); alert('Disalin!');" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20 group"><i class="fa-solid fa-copy"></i></button></div>
                </div>
            </div>
            @endif

            @if(session('success'))<div class="glass-panel p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-xs font-bold flex items-center v-reveal-scale" style="--delay: 0.8s"><i class="fa-solid fa-circle-check mr-3 text-lg"></i>{{ session('success') }}</div>@endif
            @if(session('error'))<div class="glass-panel p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 text-xs font-bold flex items-center v-reveal-scale" style="--delay: 0.8s"><i class="fa-solid fa-circle-xmark mr-3 text-lg"></i>{{ session('error') }}</div>@endif

            <!-- Page Actions -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 v-reveal-left" style="--delay: 0.4s">
                <div class="text-center lg:text-left"><h1 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tighter leading-none mb-3">User <span class="text-blue-600">Management</span></h1><p class="text-slate-400 font-bold tracking-[0.3em] uppercase text-[10px] flex items-center justify-center lg:justify-start"><i class="fa-solid fa-users-gear mr-3 text-blue-500 animate-pulse"></i> ACCESS_CONTROL_PROTOCOL_V7.2</p></div>
                <div class="flex flex-wrap items-center justify-center lg:justify-end gap-4">
                    <div class="relative group"><i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input type="text" placeholder="SEARCH_DATABASE..." class="bg-white/80 border border-slate-200 rounded-2xl pl-12 pr-6 py-3.5 text-[10px] font-black uppercase tracking-widest focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all w-full sm:w-[300px] shadow-sm"></div>
                    <div id="lockdown-btn" onclick="toggleSystemLockdown()" class="flex items-center bg-slate-900 px-5 py-3 rounded-2xl border border-slate-700 shadow-xl group cursor-pointer hover:bg-red-600 transition-all duration-500"><div class="flex flex-col items-end mr-4"><span class="text-[8px] font-black text-slate-400 group-hover:text-white uppercase tracking-widest">Global_Access</span><span id="lockdown-status-text" class="text-[9px] font-black text-emerald-400 group-hover:text-white uppercase">UNLOCKED</span></div><div id="lockdown-switch-bg" class="w-10 h-6 bg-emerald-500/20 rounded-full relative p-1 group-hover:bg-white/20 transition-colors"><div id="lockdown-switch-ball" class="w-4 h-4 bg-emerald-500 rounded-full group-hover:bg-white transition-all shadow-lg translate-x-0"></div></div></div>
                    <button onclick="toggleUserModal()" class="px-8 py-4 rounded-2xl bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-2xl shadow-blue-600/20 flex items-center justify-center"><i class="fa-solid fa-user-plus mr-3"></i> Register_New_User</button>
                </div>
            </div>

            @include('partials.it_users.metrics')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                @include('partials.it_users.user_table')
                @include('partials.it_users.activity_log')
            </div>
        </main>
    </div>

    @include('partials.it_users.registration_modal')
    @include('partials.it_users.user_scripts')
</body>
</html>
