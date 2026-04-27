<header class="flex flex-col md:flex-row items-center justify-between mb-8 pb-4 border-b border-slate-200">
    <div class="flex items-center space-x-4 mb-4 md:mb-0">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-blue-500/30">
            <i class="fa-solid fa-water text-white text-2xl"></i>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Water <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Monitoring</span></h1>
            <p class="text-sm font-medium text-slate-500 tracking-wide uppercase">Environmental Division</p>
        </div>
    </div>
    <div class="flex items-center space-x-4">
        <!-- Mode Switcher -->
        <div class="flex bg-slate-100 p-1 rounded-xl shadow-inner border border-slate-200">
            <a href="{{ route('user.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold tracking-wider transition-all {{ request()->routeIs('user.dashboard') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                <i class="fa-solid fa-user mr-1"></i> USER
            </a>
            <a href="{{ route('it.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold tracking-wider transition-all {{ request()->routeIs('it.dashboard') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                <i class="fa-solid fa-microchip mr-1"></i> IT
            </a>
        </div>

        <a href="{{ route('devices.index') }}" class="flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white shadow-lg shadow-slate-900/20 hover:bg-blue-600 transition-all text-xs font-bold tracking-widest group">
            <i class="fa-solid fa-server mr-2 opacity-50 group-hover:opacity-100"></i> MANAJEMEN PERANGKAT
        </a>
        <div id="connection-status" class="flex items-center px-4 py-2 rounded-full bg-white shadow-sm border border-slate-100 transition-colors duration-500">
            <span class="w-2.5 h-2.5 rounded-full bg-slate-400 mr-2 animate-pulse" id="connection-dot"></span>
            <span id="connection-text" class="text-xs font-bold tracking-widest text-slate-500">CONNECTING...</span>
        </div>
    </div>
</header>
