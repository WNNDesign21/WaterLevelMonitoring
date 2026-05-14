@include('partials.dashboard.head')

<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-500 z-50"></div>

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-slate-800 tracking-tighter uppercase leading-none">Water<span class="text-blue-600">Sense</span></h1>
            <p class="text-[10px] font-mono text-blue-600 tracking-[0.3em] uppercase mt-2">Account_Recovery_System</p>
        </div>

        <div class="glass-panel bg-white/80 border border-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <h2 class="text-xl font-black text-slate-800 mb-2">Lupa Password?</h2>
            <p class="text-xs text-slate-500 mb-8 font-medium leading-relaxed">Jangan khawatir, Bos. Masukkan email terdaftar Anda, dan kami akan mengirimkan link untuk mereset password Anda.</p>

            @if(session('status'))
                <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-xl text-xs font-bold uppercase tracking-widest flex items-center">
                    <i class="fa-solid fa-circle-check mr-3"></i> {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Email_Address</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs transition-colors group-focus-within:text-blue-500"></i>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-slate-50 border border-slate-100 px-11 py-3.5 rounded-2xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all shadow-sm">
                    </div>
                    @error('email')
                        <p class="text-[10px] text-red-500 mt-2 ml-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:bg-blue-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center group">
                    KIRIM VIA EMAIL <i class="fa-solid fa-envelope ml-3 group-hover:translate-x-1 transition-transform"></i>
                </button>

                <button type="submit" formaction="{{ route('password.whatsapp') }}" class="w-full bg-emerald-500 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-100 hover:bg-emerald-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center group mt-3">
                    KIRIM VIA WHATSAPP <i class="fa-solid fa-whatsapp ml-3 group-hover:scale-110 transition-transform text-lg"></i>
                </button>

                <div class="text-center mt-8">
                    <a href="{{ route('login') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
