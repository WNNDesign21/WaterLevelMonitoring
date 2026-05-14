@include('partials.dashboard.head')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    <style>
        /* JARVIS CINEMATIC ENTRY PROTOCOL */
        @keyframes revealFromLeft {
            0% { opacity: 0; transform: translateX(-100px); filter: blur(20px); }
            100% { opacity: 1; transform: translateX(0); filter: blur(0); }
        }
        @keyframes revealFromRight {
            0% { opacity: 0; transform: translateX(100px); filter: blur(20px); }
            100% { opacity: 1; transform: translateX(0); filter: blur(0); }
        }
        @keyframes revealFromBottom {
            0% { opacity: 0; transform: translateY(100px); filter: blur(20px); }
            100% { opacity: 1; transform: translateY(0); filter: blur(0); }
        }
        @keyframes revealFromTop {
            0% { opacity: 0; transform: translateY(-100px); filter: blur(20px); }
            100% { opacity: 1; transform: translateY(0); filter: blur(0); }
        }
        @keyframes revealScale {
            0% { opacity: 0; transform: scale(0.9); filter: blur(20px); }
            100% { opacity: 1; transform: scale(1); filter: blur(0); }
        }

        .v-reveal-left, .v-reveal-right, .v-reveal-bottom, .v-reveal-top, .v-reveal-scale {
            opacity: 0;
            animation-duration: 1.8s;
            animation-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1);
            animation-fill-mode: forwards;
            animation-delay: var(--delay, 0s);
        }

        body.loaded .v-reveal-left { animation-name: revealFromLeft; }
        body.loaded .v-reveal-right { animation-name: revealFromRight; }
        body.loaded .v-reveal-bottom { animation-name: revealFromBottom; }
        body.loaded .v-reveal-top { animation-name: revealFromTop; }
        body.loaded .v-reveal-scale { animation-name: revealScale; }

        @keyframes fade-in {
            from { opacity: 0; transform: translateX(10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
    </style>

<body class="min-h-screen bg-slate-50 font-sans antialiased selection:bg-blue-200 selection:text-blue-900 relative pb-10">
    <!-- Top Accent Line -->
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-500 z-50 shadow-[0_0_10px_rgba(59,130,246,0.3)]"></div>

    <div class="max-w-[2000px] mx-auto px-6 mt-6">
        <!-- HEADER: Consolidated WaterSense IT Command Center -->
        <div class="mb-10 v-reveal-top" style="--delay: 0.2s">
            <div class="glass-panel p-4 md:p-5 rounded-[2rem] bg-white/60 border border-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-6">
                <!-- Branding Section (Left) -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('it.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-slate-900 text-white rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-slate-900/20 group">
                        <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                    </a>
                    <div class="flex items-center space-x-3 border-r border-slate-200 pr-6 mr-2 hidden md:flex">
                        <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" class="w-10 h-10 object-contain drop-shadow-md">
                        <div>
                            <h1 class="text-lg font-black text-slate-800 tracking-tighter uppercase leading-none">Water<span class="text-cyan-600">Sense</span></h1>
                            <p class="text-[8px] font-mono text-cyan-600 tracking-[0.3em] uppercase mt-1">USER_HQ</p>
                        </div>
                    </div>
                    <!-- Real-time Clock -->
                    <div class="hidden xl:flex flex-col">
                        <span id="header-time" class="text-xs font-black text-slate-700 font-mono tracking-widest">00:00:00</span>
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mt-1">WIB_KARAWANG_SECTOR</span>
                    </div>
                </div>

                <!-- Central Status & Actions -->
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div class="flex items-center space-x-3 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl shadow-sm">
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 block"></span>
                            <span class="absolute inset-0 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping opacity-75"></span>
                        </div>
                        <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">USER_MANAGEMENT_MODE</span>
                    </div>

                    <div class="flex items-center bg-slate-100/50 p-1 rounded-2xl border border-slate-200/30">
                        <a href="{{ route('it.dashboard') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all">Dashboard</a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.analytics.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-blue-600 hover:text-white transition-all">Analytics</a>
                        <div class="w-px h-4 bg-slate-200 mx-1"></div>
                        <a href="{{ route('it.devices.index') }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-cyan-500 hover:text-white transition-all">Device</a>
                    </div>
                </div>

                <!-- Profile Section (Right) -->
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-[11px] font-black text-slate-800 uppercase tracking-tighter leading-none">{{ auth()->user()->name }}</div>
                        <div class="text-[8px] font-bold text-cyan-600 uppercase tracking-widest mt-1.5 flex items-center justify-end">IT_ADMIN_RANK</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0f172a&color=fff' }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-lg">
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-10 h-10 rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all shadow-lg flex items-center justify-center"><i class="fa-solid fa-power-off text-xs"></i></button>
                        </form>
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
            setInterval(updateHeaderClock, 1000);
            updateHeaderClock();
        </script>

        <main class="space-y-10">
                <!-- Alerts -->
                <!-- One-Time Password Display -->
                @if(session('success_password'))
                <div class="glass-panel p-8 rounded-[2rem] bg-blue-600/10 border border-blue-600/20 v-reveal-scale relative overflow-hidden" style="--delay: 0.8s">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <i class="fa-solid fa-key text-6xl text-blue-600"></i>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">User Berhasil Didaftarkan</span>
                            </div>
                            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Kredensial Akses <span class="text-blue-600">{{ session('success_password')['name'] }}</span></h3>
                            <p class="text-[10px] font-bold text-slate-500 mt-2 max-w-md">Harap simpan password ini. Password hanya akan ditampilkan sekali ini saja demi keamanan. User akan diminta mengganti password saat pertama kali login.</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-white px-6 py-4 rounded-2xl border border-slate-200 shadow-sm text-center">
                                <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Temporary Password</div>
                                <div class="text-xl font-black text-blue-600 font-mono tracking-wider" id="tempPass">{{ session('success_password')['password'] }}</div>
                            </div>
                            <button onclick="copyPassword()" class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20 group">
                                <i class="fa-solid fa-copy group-active:scale-90 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <script>
                    function copyPassword() {
                        const pass = document.getElementById('tempPass').innerText;
                        navigator.clipboard.writeText(pass);
                        alert('Password User berhasil disalin!');
                    }
                </script>
                @endif

                @if(session('success'))
                <div class="glass-panel p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-xs font-bold flex items-center v-reveal-scale" style="--delay: 0.8s">
                    <i class="fa-solid fa-circle-check mr-3 text-lg"></i>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="glass-panel p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 text-xs font-bold flex items-center v-reveal-scale" style="--delay: 0.8s">
                    <i class="fa-solid fa-circle-xmark mr-3 text-lg"></i>
                    {{ session('error') }}
                </div>
                @endif

                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 v-reveal-left" style="--delay: 0.4s">
                    <div class="text-center lg:text-left">
                        <h1 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tighter leading-none mb-3">
                            User <span class="text-blue-600">Management</span>
                        </h1>
                        <p class="text-slate-400 font-bold tracking-[0.3em] uppercase text-[10px] flex items-center justify-center lg:justify-start">
                            <i class="fa-solid fa-users-gear mr-3 text-blue-500 animate-pulse"></i> ACCESS_CONTROL_PROTOCOL_V7.2
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center justify-center lg:justify-end gap-4">
                        <!-- Advanced Search -->
                        <div class="relative group">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                            <input type="text" placeholder="SEARCH_DATABASE..." class="bg-white/80 border border-slate-200 rounded-2xl pl-12 pr-6 py-3.5 text-[10px] font-black uppercase tracking-widest focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all w-full sm:w-[300px] shadow-sm">
                        </div>

                        <!-- Lockdown Toggle (Functional) -->
                        <div id="lockdown-btn" onclick="toggleSystemLockdown()" class="flex items-center bg-slate-900 px-5 py-3 rounded-2xl border border-slate-700 shadow-xl group cursor-pointer hover:bg-red-600 transition-all duration-500">
                            <div class="flex flex-col items-end mr-4">
                                <span class="text-[8px] font-black text-slate-400 group-hover:text-white uppercase tracking-widest">Global_Access</span>
                                <span id="lockdown-status-text" class="text-[9px] font-black text-emerald-400 group-hover:text-white uppercase">UNLOCKED</span>
                            </div>
                            <div id="lockdown-switch-bg" class="w-10 h-6 bg-emerald-500/20 rounded-full relative p-1 group-hover:bg-white/20 transition-colors">
                                <div id="lockdown-switch-ball" class="w-4 h-4 bg-emerald-500 rounded-full group-hover:bg-white transition-all shadow-lg shadow-emerald-500/50"></div>
                            </div>
                        </div>

                        <button onclick="toggleUserModal()" class="px-8 py-4 rounded-2xl bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-blue-700 transition-all shadow-2xl shadow-blue-600/20 flex items-center justify-center">
                            <i class="fa-solid fa-user-plus mr-3"></i> Register_New_User
                        </button>
                    </div>
                </div>

                <!-- OVERPOWERED SECURITY METRICS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 v-reveal-bottom" style="--delay: 0.6s">
                    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-blue-500 transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-shield-halved text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Auth_Status</h4>
                                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">INTEGRITY_SAFE</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[8px] font-mono text-emerald-500 font-bold">100.0%</div>
                            <div class="w-12 h-1 bg-emerald-100 rounded-full overflow-hidden mt-1">
                                <div class="bg-emerald-500 h-full w-full"></div>
                            </div>
                        </div>
                    </div>

                    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-cyan-500 transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                <i class="fa-solid fa-users text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active_Admins</h4>
                                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">02_OPERATIONAL</div>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="w-2 h-2 rounded-full bg-cyan-500 animate-ping"></div>
                        </div>
                    </div>

                    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-amber-500 transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                                <i class="fa-solid fa-satellite-dish text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System_Uplink</h4>
                                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">ONLINE</div>
                            </div>
                        </div>
                        <div class="text-[9px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">SYNCED</div>
                    </div>

                    <div class="glass-panel p-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between group hover:border-blue-500 transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-900 flex items-center justify-center text-white">
                                <i class="fa-solid fa-fingerprint text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Last_Sync</h4>
                                <div class="text-lg font-black text-slate-800 tracking-tighter uppercase">{{ now()->format('H:i') }}_WIB</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-200 text-xs"></i>
                    </div>
                </div>

                <!-- DUAL COLUMN COMMAND GRID -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    <!-- LEFT: MAIN USER MATRIX (9 Cols) -->
                    <div class="lg:col-span-9 space-y-6">
                        <div class="v-reveal-left" style="--delay: 1.0s">
                            <!-- Desktop Table View -->
                            <div class="hidden md:block glass-panel rounded-[2.5rem] border border-white shadow-2xl bg-white/40 backdrop-blur-xl overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">User_Identity</th>
                                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Contact_Uplink</th>
                                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Role_Access</th>
                                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100/50">
                                            @foreach($users as $user)
                                            <tr class="hover:bg-white/60 transition-all group">
                                                <td class="px-8 py-6">
                                                    <div class="flex items-center space-x-5">
                                                        <div class="relative shrink-0">
                                                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=3b82f6&color=fff' }}" class="w-14 h-14 rounded-2xl object-cover shadow-xl group-hover:rotate-3 transition-transform duration-500 border-2 border-white">
                                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                                                        </div>
                                                        <div>
                                                            <h3 class="font-black text-slate-800 text-base tracking-tight leading-none mb-2">{{ $user->name }}</h3>
                                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></span> SINCE_{{ strtoupper($user->created_at->format('M_Y')) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <div class="text-sm font-bold text-slate-600 flex items-center">
                                                        <i class="fa-solid fa-envelope mr-3 text-slate-300"></i> {{ $user->email }}
                                                    </div>
                                                    <div class="text-[10px] font-black text-slate-400 mt-2 flex items-center">
                                                        <i class="fa-solid fa-phone mr-3 text-slate-300"></i> {{ $user->phone ?? '---_---_----' }}
                                                    </div>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <form action="{{ route('it.users.update-role', $user->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <select name="role" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer shadow-sm min-w-[200px]">
                                                            <option value="Administrator IT" {{ $user->role === 'Administrator IT' ? 'selected' : '' }}>Administrator IT</option>
                                                            <option value="Operator Pusat Kendali" {{ $user->role === 'Operator Pusat Kendali' ? 'selected' : '' }}>Operator Pusat Kendali</option>
                                                            <option value="Teknisi Lapangan" {{ $user->role === 'Teknisi Lapangan' ? 'selected' : '' }}>Teknisi Lapangan</option>
                                                            <option value="Pejabat Berwenang" {{ $user->role === 'Pejabat Berwenang' ? 'selected' : '' }}>Pejabat Berwenang</option>
                                                            <option value="Warga" {{ $user->role === 'Warga' ? 'selected' : '' }}>Warga</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td class="px-8 py-6">
                                                    <div class="flex items-center space-x-2">
                                                        @if($user->id !== auth()->id())
                                                        <form action="{{ route('it.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-red-100 shadow-sm group/btn">
                                                                <i class="fa-solid fa-trash-can text-xs group-hover/btn:scale-110 transition-transform"></i>
                                                            </button>
                                                        </form>
                                                        @else
                                                        <div class="px-4 py-2 rounded-xl bg-blue-50 text-blue-600 text-[8px] font-black uppercase tracking-widest border border-blue-100 italic">Self_Node</div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination (Inside Matrix) -->
                        <div class="v-reveal-item delay-2 glass-panel px-8 py-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Matrix_Range: {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}</span>
                            <div>{{ $users->links() }}</div>
                        </div>
                    </div>

                    <!-- RIGHT: SYSTEM ACTIVITY LOG (3 Cols) -->
                    <aside class="lg:col-span-3 space-y-6">
                        <!-- Access Trend Chart -->
                        <div class="v-reveal-right glass-panel p-6 rounded-[2.5rem] bg-white border border-slate-100 shadow-xl overflow-hidden" style="--delay: 1.2s">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">Access_Density</h3>
                                <span class="text-[8px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">24H_HISTORY</span>
                            </div>
                            <div id="accessTrendChart" style="height: 140px;" class="w-full"></div>
                        </div>

                        <div class="v-reveal-right glass-panel p-6 rounded-[2.5rem] bg-white border border-slate-100 shadow-xl relative overflow-hidden h-full flex flex-col" style="--delay: 1.6s">
                            <div class="absolute top-0 right-0 p-8 opacity-5">
                                <i class="fa-solid fa-satellite-dish text-8xl"></i>
                            </div>
                            
                            <h3 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em] mb-6 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-3 animate-pulse"></span> Access_Event_Stream
                            </h3>

                            <div id="activity-log-stream" class="flex-1 space-y-6 overflow-y-auto pr-2 custom-scrollbar">
                                <!-- Live Log Items will appear here -->
                                <div class="flex items-center justify-center py-10">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-slate-100">
                                <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                                    <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1">Infrastructure_Load</p>
                                    <div class="w-full bg-blue-200/50 h-1 rounded-full overflow-hidden mt-2">
                                        <div class="bg-blue-600 h-full w-[42%]"></div>
                                    </div>
                                    <p class="text-[7px] text-blue-400 font-mono mt-2 uppercase tracking-tighter text-right">0.42_NODE_USAGE</p>
                                </div>

                                <a href="{{ route('it.users.history') }}" class="mt-4 w-full py-4 rounded-2xl border border-slate-200 bg-slate-50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-500 flex items-center justify-center group shadow-sm">
                                    <i class="fa-solid fa-clock-rotate-left mr-3 opacity-50 group-hover:rotate-[-45deg] transition-all"></i> VIEW_FULL_CHRONOLOGY
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
        </main>
    </div>

    <!-- Recruitment Modal -->
    <div id="userModal" class="fixed inset-0 z-[1000] invisible opacity-0 transition-all duration-500 overflow-y-auto">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleUserModal()"></div>
            
            <div class="inline-block w-full max-w-xl p-5 md:p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white/90 backdrop-blur-2xl shadow-[0_40px_80px_rgba(0,0,0,0.2)] rounded-[2rem] md:rounded-[2.5rem] border border-white relative z-10 scale-90 opacity-0 duration-500" id="userModalContent">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">User <span class="text-blue-600">Registration</span></h2>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Daftarkan User Baru ke Sistem</p>
                    </div>
                    <button onclick="toggleUserModal()" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form action="{{ route('it.users.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Nama Lengkap</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200/50 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="Masukkan nama...">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Email Instansi</label>
                            <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200/50 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none" placeholder="email@example.com">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">User Role</label>
                        <select name="role" required class="w-full bg-slate-50 border border-slate-200/50 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none cursor-pointer">
                            <option value="Warga">Warga</option>
                            <option value="Teknisi Lapangan">Teknisi Lapangan</option>
                            <option value="Operator Pusat Kendali">Operator Pusat Kendali</option>
                            <option value="Pejabat Berwenang">Pejabat Berwenang</option>
                            <option value="Administrator IT">Administrator IT</option>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] hover:shadow-xl hover:shadow-blue-500/20 hover:-translate-y-0.5 transition-all duration-300 shadow-lg">
                            Daftarkan User Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reveal & Modal Script -->
    <script>
        function toggleUserModal() {
            const modal = document.getElementById('userModal');
            const content = document.getElementById('userModalContent');
            if (modal.classList.contains('invisible')) {
                modal.classList.remove('invisible', 'opacity-0');
                setTimeout(() => {
                    content.classList.remove('scale-90', 'opacity-0');
                }, 10);
            } else {
                content.classList.add('scale-90', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('invisible', 'opacity-0');
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                if(!document.body.classList.contains('loaded')) {
                    document.body.classList.add('loaded');
                }
                initAccessTrendChart();
            }, 300); // 300ms delay for desktop eye-sync
        });

        function initAccessTrendChart() {
            const chartDom = document.getElementById('accessTrendChart');
            if(!chartDom) return;
            const myChart = echarts.init(chartDom);
            const option = {
                grid: { top: 10, bottom: 20, left: 10, right: 10 },
                xAxis: {
                    type: 'category',
                    data: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                    axisLine: { show: false },
                    axisTick: { show: false },
                    axisLabel: { color: '#94a3b8', fontSize: 8, fontWeight: 'bold' }
                },
                yAxis: { type: 'value', show: false },
                series: [{
                    data: [12, 45, 67, 34, 89, 56],
                    type: 'line',
                    smooth: true,
                    showSymbol: false,
                    lineStyle: { width: 3, color: '#3b82f6' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(59, 130, 246, 0.2)' },
                            { offset: 1, color: 'rgba(59, 130, 246, 0)' }
                        ])
                    }
                }]
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        // --- SYSTEM LOCKDOWN LOGIC ---
        function toggleSystemLockdown() {
            if(!confirm('MASTER_KILL_SWITCH: Apakah Anda yakin ingin mengubah status akses global?')) return;

            fetch("{{ route('it.system.lockdown.toggle') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                updateLockdownUI(data.lockdown);
                alert(data.message);
            })
            .catch(error => console.error('Error:', error));
        }

        function updateLockdownUI(status) {
            const text = document.getElementById('lockdown-status-text');
            const bg = document.getElementById('lockdown-switch-bg');
            const ball = document.getElementById('lockdown-switch-ball');
            const btn = document.getElementById('lockdown-btn');

            if (status === '1') {
                text.innerText = 'LOCKED';
                text.classList.remove('text-emerald-400');
                text.classList.add('text-red-500');
                bg.classList.remove('bg-emerald-500/20');
                bg.classList.add('bg-red-500/20');
                ball.classList.remove('bg-emerald-500', 'translate-x-0');
                ball.classList.add('bg-red-500', 'translate-x-4');
                btn.classList.add('border-red-500/50', 'ring-4', 'ring-red-500/10');
            } else {
                text.innerText = 'UNLOCKED';
                text.classList.remove('text-red-500');
                text.classList.add('text-emerald-400');
                bg.classList.remove('bg-red-500/20');
                bg.classList.add('bg-emerald-500/20');
                ball.classList.remove('bg-red-500', 'translate-x-4');
                ball.classList.add('bg-emerald-500', 'translate-x-0');
                btn.classList.remove('border-red-500/50', 'ring-4', 'ring-red-500/10');
            }
        }

        // Initial Status Check
        fetch("{{ route('it.system.status') }}")
            .then(response => response.json())
            .then(data => updateLockdownUI(data.lockdown));

        // --- LIVE LOG STREAM LOGIC ---
        function fetchActivityLogs() {
            fetch("{{ route('it.system.logs') }}")
                .then(response => response.json())
                .then(logs => {
                    const container = document.getElementById('activity-log-stream');
                    if (!container) return;
                    
                    if (logs.length === 0) {
                        container.innerHTML = '<div class="text-center py-10 text-[10px] font-black text-slate-400 uppercase tracking-widest">No activity recorded</div>';
                        return;
                    }

                    container.innerHTML = logs.map(log => `
                        <div class="flex space-x-4 group animate-fade-in">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-lg ${getEventBg(log.event)} flex items-center justify-center text-white text-[10px] font-black border shadow-sm transition-all">
                                    ${log.user.charAt(0)}
                                </div>
                                <div class="w-px h-full bg-slate-100 my-2"></div>
                            </div>
                            <div class="pb-4">
                                <div class="text-[10px] font-black text-slate-800 uppercase tracking-tight">${log.user}</div>
                                <div class="text-[8px] font-bold ${getEventText(log.event)} uppercase tracking-widest mt-1">${log.desc}</div>
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="text-[8px] font-mono text-slate-400"><i class="fa-solid fa-clock mr-1"></i> ${log.time}</span>
                                    <span class="text-[8px] font-mono text-blue-500/60 bg-blue-50 px-1.5 py-0.5 rounded-md border border-blue-100/50">${log.ip}</span>
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => console.error('Error fetching logs:', error));
        }

        function getEventBg(event) {
            if (event.includes('failed')) return 'bg-red-500 border-red-600 shadow-red-200';
            if (event.includes('success')) return 'bg-emerald-500 border-emerald-600 shadow-emerald-200';
            if (event.includes('sso')) return 'bg-blue-500 border-blue-600 shadow-blue-200';
            return 'bg-slate-500 border-slate-600 shadow-slate-200';
        }

        function getEventText(event) {
            if (event.includes('failed')) return 'text-red-500';
            if (event.includes('success')) return 'text-emerald-500';
            if (event.includes('sso')) return 'text-blue-500';
            return 'text-slate-500';
        }

        // Start Polling
        fetchActivityLogs();
        setInterval(fetchActivityLogs, 5000); // Update every 5 seconds
    </script>
</body>
</html>
