@include('partials.dashboard.head')

<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-500 z-50"></div>

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-slate-800 tracking-tighter uppercase leading-none">Water<span class="text-blue-600">Sense</span></h1>
            <p class="text-[10px] font-mono text-blue-600 tracking-[0.3em] uppercase mt-2">Secure_Reset_Protocol</p>
        </div>

        <div class="glass-panel bg-white/80 border border-white p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl"></div>
            
            <h2 class="text-xl font-black text-slate-800 mb-2">Reset Password</h2>
            <p class="text-xs text-slate-500 mb-8 font-medium leading-relaxed">Silakan masukkan password baru yang kuat untuk mengamankan kembali akun WaterSense Anda.</p>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Email_Identity</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly class="w-full bg-slate-100 border border-slate-100 px-6 py-3.5 rounded-2xl text-sm text-slate-500 font-bold focus:outline-none cursor-not-allowed">
                    @error('email')
                        <p class="text-[10px] text-red-500 mt-2 ml-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">New_Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                        <input type="password" name="password" required placeholder="Min 8 Karakter" class="w-full bg-slate-50 border border-slate-100 px-11 py-3.5 rounded-2xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all shadow-sm">
                    </div>
                    @error('password')
                        <p class="text-[10px] text-red-500 mt-2 ml-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Confirm_New_Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-shield-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi Password" class="w-full bg-slate-50 border border-slate-100 px-11 py-3.5 rounded-2xl text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all shadow-sm">
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-200 hover:bg-blue-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center group mt-4">
                    PERBARUI PASSWORD <i class="fa-solid fa-key ml-3 group-hover:rotate-45 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
