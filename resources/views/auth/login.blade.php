@include('partials.dashboard.head')

<body class="min-h-screen bg-slate-50 font-sans antialiased selection:bg-blue-200 selection:text-blue-900 relative flex items-center justify-center py-10 px-4 md:px-6">
    <!-- Full Screen Animated Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-400/20 blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-cyan-400/20 blur-[120px] animate-pulse delay-700"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-5xl flex flex-col lg:flex-row items-stretch justify-center gap-6 lg:gap-10 v-reveal-item">
        
        <!-- Left Panel: Glass Branding -->
        <div class="hidden lg:flex lg:w-5/12 items-center">
            <div class="glass-panel w-full p-10 lg:p-12 rounded-[3rem] border border-white/60 shadow-2xl backdrop-blur-xl text-center space-y-8">
                <div class="inline-flex p-5 rounded-[2.5rem] bg-white shadow-xl shadow-blue-500/10 mb-4">
                    <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense" class="w-16 h-16 object-contain">
                </div>
                <div>
                    <h1 class="text-4xl font-black text-slate-800 tracking-tighter leading-tight">
                        Penjaga <span class="text-blue-600">Digital</span> <br>Sungai Citarum.
                    </h1>
                    <p class="mt-6 text-slate-500 font-bold text-xs leading-relaxed uppercase tracking-widest opacity-80">
                        Sistem informasi hidrologi cerdas untuk keselamatan warga Karawang.
                    </p>
                </div>
                <div class="flex flex-col space-y-3 pt-6">
                    <div class="px-6 py-3 rounded-2xl bg-white/50 border border-white text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] shadow-sm">
                        <i class="fa-solid fa-satellite-dish mr-2 text-blue-500"></i> Real-time Telemetry
                    </div>
                    <div class="px-6 py-3 rounded-2xl bg-white/50 border border-white text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] shadow-sm">
                        <i class="fa-solid fa-shield-check mr-2 text-cyan-500"></i> Safety Guaranteed
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-full lg:w-6/12 flex items-center justify-center">
            <div class="w-full space-y-8">
                <!-- Mobile Logo Header -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex p-4 rounded-2xl bg-white shadow-lg mb-4">
                        <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense" class="w-12 h-12">
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">WaterSense Access</h2>
                </div>

                <div class="glass-panel w-full rounded-[2.5rem] border border-white/80 shadow-2xl p-8 lg:p-10 relative bg-white/60 backdrop-blur-2xl">
                    <div class="mb-8">
                        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Akses Portal</h2>
                        <p class="text-slate-500 font-bold tracking-widest uppercase text-[9px] mt-2">Silakan masuk untuk melanjutkan proteksi</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <!-- Google SSO Button -->
                        <a href="http://103.172.205.35.nip.io/auth/google" class="w-full flex items-center justify-center bg-white border border-slate-200 py-3.5 rounded-2xl shadow-sm hover:shadow-md hover:bg-slate-50 transition-all duration-300 group">
                            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform" alt="Google">
                            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Sign in with Google</span>
                        </a>

                        <div class="relative py-1 flex items-center">
                            <div class="flex-grow border-t border-slate-200/50"></div>
                            <span class="flex-shrink mx-4 text-[8px] font-black text-slate-400 uppercase tracking-widest">Atau Manual</span>
                            <div class="flex-grow border-t border-slate-200/50"></div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-white/80 border {{ $errors->has('email') ? 'border-red-400 ring-2 ring-red-500/10' : 'border-slate-200/60' }} py-3.5 px-6 rounded-2xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="name@example.com">
                                @error('email')
                                    <p class="text-[9px] font-bold text-red-500 mt-2 ml-1 uppercase tracking-wider"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-2 ml-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Secure Password</label>
                                    <a href="#" class="text-[9px] font-bold text-blue-500 uppercase tracking-widest hover:text-blue-700">Lupa?</a>
                                </div>
                                <input type="password" name="password" required class="w-full bg-white/80 border {{ $errors->has('password') ? 'border-red-400 ring-2 ring-red-500/10' : 'border-slate-200/60' }} py-3.5 px-6 rounded-2xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="••••••••">
                                @error('password')
                                    <p class="text-[9px] font-bold text-red-500 mt-2 ml-1 uppercase tracking-wider"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-2 ml-1">
                            <label class="flex items-center space-x-2 cursor-pointer group">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-200 text-blue-600 focus:ring-blue-500/20 transition-all">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-slate-600 transition-colors">Ingat Saya</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-[10px] font-black text-blue-500 uppercase tracking-widest hover:text-blue-700 transition-colors">Lupa Password?</a>
                        </div>

                        <div class="pt-2 space-y-4">
                            <button type="submit" class="w-full bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.25em] shadow-[0_20px_40px_-15px_rgba(15,23,42,0.4)] hover:shadow-[0_20px_40px_-10px_rgba(59,130,246,0.3)] hover:-translate-y-1 hover:from-blue-600 hover:to-blue-700 transition-all duration-500 flex items-center justify-center group relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <span class="relative z-10 flex items-center">
                                    Enter Dashboard 
                                    <i class="fa-solid fa-shield-halved ml-3 text-[10px] opacity-50 group-hover:opacity-100 group-hover:rotate-12 transition-all"></i>
                                </span>
                            </button>

                            <div class="text-center">
                                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center justify-center w-full py-4 rounded-2xl border border-cyan-200 bg-cyan-500/10 text-[9px] font-black uppercase tracking-[0.2em] text-cyan-700 hover:bg-cyan-600 hover:text-white hover:border-cyan-600 transition-all duration-500 group shadow-sm shadow-cyan-500/10">
                                    <i class="fa-solid fa-eye-low-vision mr-3 opacity-70 group-hover:scale-125 transition-transform"></i> Monitor Sebagai Tamu
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="text-center pt-8 mt-8 border-t border-slate-100/50">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 font-black hover:underline">Daftar Sekarang</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.add('loaded');
            }, 100);
        });
    </script>
</body>
</html>
