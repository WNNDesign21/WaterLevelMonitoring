@include('partials.dashboard.head')

<body class="min-h-screen bg-slate-50 font-sans antialiased selection:bg-blue-500/30 overflow-x-hidden flex items-center justify-center p-6">
    <!-- Animated Background Elements -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-100 rounded-full blur-[120px] opacity-60"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-cyan-100 rounded-full blur-[120px] opacity-60"></div>
    </div>

    <div class="w-full max-w-md v-reveal-item">
        <!-- Logo & Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-white border border-slate-200 shadow-xl mb-6 group">
                <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" class="w-12 h-12 object-contain transition-transform duration-700 group-hover:rotate-[360deg]" alt="WaterSense">
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Keamanan <span class="text-blue-600">Sentinel</span></h1>
            <p class="text-slate-400 font-bold tracking-[0.2em] uppercase text-[10px] mt-4">Wajib Ganti Password Pertama Kali</p>
        </div>

        <!-- Luxury Light Card -->
        <div class="bg-white/80 backdrop-blur-xl border border-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
            
            <form action="{{ route('password.change.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Password Baru</label>
                    <div class="relative group">
                        <i class="fa-solid fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="password" name="password" required 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-5 py-4 text-slate-700 text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/30 transition-all outline-none" 
                               placeholder="Min. 8 Karakter">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Konfirmasi Password</label>
                    <div class="relative group">
                        <i class="fa-solid fa-shield-check absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-cyan-500 transition-colors"></i>
                        <input type="password" name="password_confirmation" required 
                               class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-5 py-4 text-slate-700 text-sm font-bold focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500/30 transition-all outline-none" 
                               placeholder="Ulangi Password">
                    </div>
                </div>

                @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-500 text-[10px] font-bold">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <button type="submit" class="w-full py-5 rounded-2xl bg-slate-900 text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-900/20 hover:bg-blue-600 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center">
                    Aktifkan Akses <i class="fa-solid fa-chevron-right ml-3 text-[10px]"></i>
                </button>
            </form>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-8">
            <p class="text-slate-400 text-[9px] font-bold uppercase tracking-[0.1em]">
                <i class="fa-solid fa-user-shield mr-2 text-blue-500"></i> Koneksi Terenkripsi AES-256 BIT
            </p>
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
