<header class="flex flex-col md:flex-row items-center justify-between mb-6 md:mb-8 pb-4 border-b border-slate-200 gap-4">
    <div class="flex items-center space-x-3 md:space-x-4">
        <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center shrink-0">
            <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense Logo" class="w-10 h-10 md:w-12 md:h-12 object-contain drop-shadow-md">
        </div>
        <div>
            <h1 class="text-xl md:text-3xl font-bold text-slate-800 tracking-tight leading-none">Water<span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Sense</span></h1>
            <p class="text-[9px] md:text-sm font-medium text-slate-500 tracking-wide uppercase mt-1">Smart Hydrology System</p>
        </div>
    </div>
    
    <div class="flex items-center justify-between w-full md:w-auto gap-2 md:gap-4">
        <div class="flex items-center space-x-2 md:space-x-4">
            @if(!request()->routeIs('user.dashboard') && !request()->routeIs('user.dashboard.device'))
            <!-- Mode Switcher -->
            <div class="flex bg-slate-100 p-1 rounded-xl shadow-inner border border-slate-200 shrink-0">
                <a href="{{ route('user.dashboard') }}" class="px-2 md:px-3 py-1.5 rounded-lg text-[9px] md:text-xs font-bold tracking-wider transition-all {{ request()->routeIs('user.dashboard') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <span class="hidden xs:inline">USER</span><i class="fa-solid fa-user xs:hidden"></i>
                </a>
                <a href="{{ route('it.dashboard') }}" class="px-2 md:px-3 py-1.5 rounded-lg text-[9px] md:text-xs font-bold tracking-wider transition-all {{ request()->routeIs('it.dashboard') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                    <span class="hidden xs:inline">IT</span><i class="fa-solid fa-microchip xs:hidden"></i>
                </a>
            </div>

            @if(auth()->check() && auth()->user()->role === 'Administrator IT')
            <a href="{{ route('it.devices.index') }}" class="flex items-center px-3 md:px-4 py-2 rounded-xl bg-slate-900 text-white shadow-lg shadow-slate-900/20 hover:bg-blue-600 transition-all text-[9px] md:text-xs font-bold tracking-widest group">
                <i class="fa-solid fa-server md:mr-2 opacity-50 group-hover:opacity-100"></i>
                <span class="hidden sm:inline">MANAJEMEN PERANGKAT</span>
            </a>
            @endif
            @endif
            
            @auth
            <div class="flex items-center space-x-2 md:space-x-3 pr-2 md:pr-4 border-r border-slate-200">
                <div class="text-right">
                    <p class="text-[8px] md:text-[10px] font-black text-slate-800 leading-none uppercase">{{ Auth::user()->name }}</p>
                    <p class="text-[7px] md:text-[8px] font-bold text-blue-500 uppercase tracking-widest mt-1">Protected Citizen</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all flex items-center justify-center group" title="Logout">
                        <i class="fa-solid fa-power-off text-xs md:text-sm group-hover:scale-110 transition-transform"></i>
                    </button>
                </form>
            </div>
            @else
            <!-- Luxury Global Auth Portal -->
            <div class="flex items-center space-x-2 pr-2 md:pr-4 border-r border-slate-200/60">
                <div class="flex items-center p-1 bg-white/40 backdrop-blur-xl border border-white/80 rounded-2xl shadow-xl shadow-blue-900/5 group/auth-hub">
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:text-blue-600 hover:bg-white/80 transition-all duration-500 flex items-center">
                        <i class="fa-solid fa-user-lock mr-2 opacity-40"></i>
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-500/30 hover:shadow-cyan-400/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-500 flex items-center group/reg-btn">
                        <div class="relative flex items-center">
                            <i class="fa-solid fa-id-card-clip mr-2 text-[11px] group-hover/reg-btn:rotate-12 transition-transform"></i>
                            <span>Daftar</span>
                            <div class="absolute -top-1 -right-1 w-2 h-2 bg-white rounded-full animate-ping opacity-20"></div>
                        </div>
                    </a>
                </div>
            </div>
            @endauth
        </div>

        <div id="connection-status" class="flex items-center px-3 md:px-4 py-1.5 md:py-2 rounded-full bg-white shadow-sm border border-slate-100 shrink-0">
            <span class="w-2 md:w-2.5 h-2 md:h-2.5 rounded-full bg-slate-400 mr-2 animate-pulse" id="connection-dot"></span>
            <span id="connection-text" class="text-[8px] md:text-xs font-bold tracking-widest text-slate-500 uppercase">UP</span>
        </div>
    </div>
</header>
