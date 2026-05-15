@include('partials.dashboard.head')

<style>
    /* JARVIS HUD ANIMATIONS */
    @keyframes scanline {
        0% { transform: translateY(-100%); }
        100% { transform: translateY(100vh); }
    }
    
    @keyframes circuit-glow {
        0%, 100% { border-color: rgba(59, 130, 246, 0.3); box-shadow: 0 0 15px rgba(59, 130, 246, 0.1); }
        50% { border-color: rgba(34, 211, 238, 0.6); box-shadow: 0 0 30px rgba(34, 211, 238, 0.3); }
    }

    @keyframes holographic-pulse {
        0% { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)) brightness(1); }
        50% { filter: drop-shadow(0 0 20px rgba(34, 211, 238, 0.8)) brightness(1.2); }
        100% { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)) brightness(1); }
    }

    .jarvis-scanline {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: linear-gradient(to bottom, transparent, rgba(34, 211, 238, 0.05), transparent);
        opacity: 0.5;
        pointer-events: none;
        z-index: 50;
        animation: scanline 8s linear infinite;
    }

    .hud-reveal {
        opacity: 0;
        transform: translateY(20px);
        animation: hud-slide-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes hud-slide-up {
        to { opacity: 1; transform: translateY(0); }
    }

    .glass-panel {
        animation: circuit-glow 4s ease-in-out infinite;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-panel:hover {
        transform: scale(1.01) translateY(-5px);
        background: rgba(255, 255, 255, 0.7);
    }
</style>

<body class="min-h-screen bg-slate-900 font-sans antialiased selection:bg-blue-200 selection:text-blue-900 relative flex items-center justify-center py-10 px-4 md:px-6 overflow-hidden">
    <!-- Jarvis Scanline HUD -->
    <div class="jarvis-scanline"></div>

    <!-- Full Screen Animated Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-600/10 blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-cyan-600/10 blur-[120px] animate-pulse delay-700"></div>
        
        <!-- Animated Grid Background -->
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-5xl flex flex-col lg:flex-row items-stretch justify-center gap-6 lg:gap-10">
        
        <!-- Left Panel: Glass Branding -->
        <div class="hidden lg:flex lg:w-5/12 items-center hud-reveal" style="animation-delay: 0.1s">
            <div class="glass-panel w-full p-10 lg:p-12 rounded-[3rem] border border-white/20 shadow-2xl backdrop-blur-xl text-center space-y-8 bg-white/10">
                <div class="inline-flex p-5 rounded-[2.5rem] bg-white shadow-xl shadow-blue-500/10 mb-4 animate-[holographic-pulse_3s_infinite]">
                    <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense" class="w-16 h-16 object-contain">
                </div>
                <div>
                    <h1 class="text-4xl font-black text-white tracking-tighter leading-tight">
                        Water <span class="text-blue-400">Sense</span> <br><span class="text-2xl opacity-80">Pemantauan Sungai Secara Digital.</span>
                    </h1>
                    <p class="mt-6 text-slate-300 font-bold text-xs leading-relaxed uppercase tracking-widest opacity-80">
                        Sistem informasi hidrologi cerdas untuk keselamatan warga.
                    </p>
                </div>
                <div class="flex flex-col space-y-3 pt-6">
                    <div class="px-6 py-3 rounded-2xl bg-white/5 border border-white/10 text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] shadow-sm">
                        <i class="fa-solid fa-satellite-dish mr-2 text-blue-400"></i> Real-time Telemetry
                    </div>
                    <div class="px-6 py-3 rounded-2xl bg-white/5 border border-white/10 text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] shadow-sm">
                        <i class="fa-solid fa-shield-check mr-2 text-cyan-400"></i> Safety Guaranteed
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-full lg:w-6/12 flex items-center justify-center hud-reveal" style="animation-delay: 0.3s">
            <div class="w-full space-y-8">
                <!-- Mobile Logo Header -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex p-4 rounded-2xl bg-white shadow-lg mb-4">
                        <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense" class="w-12 h-12">
                    </div>
                    <h2 class="text-2xl font-black text-white tracking-tight">WaterSense Access</h2>
                </div>

                <div class="glass-panel w-full rounded-[2.5rem] border border-white/20 shadow-2xl p-8 lg:p-10 relative bg-white/10 backdrop-blur-2xl">
                    <div class="mb-8">
                        <h2 class="text-3xl font-black text-white tracking-tight leading-none">Akses Portal</h2>
                        <p class="text-slate-400 font-bold tracking-widest uppercase text-[9px] mt-2">Silakan masuk untuk melanjutkan proteksi</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <!-- Google SSO Button -->
                        <a href="http://103.172.205.35.nip.io/auth/google" class="w-full flex items-center justify-center bg-white border border-slate-200 py-3.5 rounded-2xl shadow-sm hover:shadow-md hover:bg-slate-50 transition-all duration-300 group">
                            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-4 h-4 mr-3 group-hover:scale-110 transition-transform" alt="Google">
                            <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">Sign in with Google</span>
                        </a>

                        <div class="relative py-1 flex items-center">
                            <div class="flex-grow border-t border-white/10"></div>
                            <span class="flex-shrink mx-4 text-[8px] font-black text-slate-400 uppercase tracking-widest">Atau Manual</span>
                            <div class="flex-grow border-t border-white/10"></div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-white/10 border {{ $errors->has('email') ? 'border-red-400 ring-2 ring-red-500/10' : 'border-white/10' }} py-3.5 px-6 rounded-2xl text-xs font-bold text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-500" placeholder="name@example.com">
                                @error('email')
                                    <p class="text-[9px] font-bold text-red-400 mt-2 ml-1 uppercase tracking-wider"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-2 ml-1">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Secure Password</label>
                                    <a href="#" class="text-[9px] font-bold text-blue-400 uppercase tracking-widest hover:text-blue-300">Lupa?</a>
                                </div>
                                <input type="password" name="password" required class="w-full bg-white/10 border {{ $errors->has('password') ? 'border-red-400 ring-2 ring-red-500/10' : 'border-white/10' }} py-3.5 px-6 rounded-2xl text-xs font-bold text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-500" placeholder="••••••••">
                                @error('password')
                                    <p class="text-[9px] font-bold text-red-400 mt-2 ml-1 uppercase tracking-wider"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-2 ml-1">
                            <label class="flex items-center space-x-2 cursor-pointer group">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/10 bg-white/5 text-blue-600 focus:ring-blue-500/20 transition-all">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-slate-200 transition-colors">Ingat Saya</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-[10px] font-black text-blue-400 uppercase tracking-widest hover:text-blue-300 transition-colors">Lupa Password?</a>
                        </div>

                        <div class="pt-2 space-y-4">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-600 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.25em] shadow-[0_20px_40px_-15px_rgba(59,130,246,0.4)] hover:shadow-[0_20px_40px_-10px_rgba(59,130,246,0.6)] hover:-translate-y-1 transition-all duration-500 flex items-center justify-center group relative overflow-hidden">
                                <span class="relative z-10">Inisialisasi Akses</span>
                                <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
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
