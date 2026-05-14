@guest
<!-- Welcome Portal for Guests -->
<div id="welcome-portal" class="fixed inset-0 z-[9999] flex items-center justify-center p-6 transition-all duration-700 opacity-0 invisible">
    <!-- Subtle Backdrop Blur -->
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md"></div>
    
    <!-- Portal Card -->
    <div class="glass-panel w-full max-w-lg rounded-[3rem] border border-white/40 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.3)] p-10 md:p-14 relative z-10 v-reveal-item scale-90 transition-transform duration-500" id="portal-card">
        <!-- Decorative Icon -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-24 h-24 rounded-3xl bg-white shadow-2xl flex items-center justify-center p-4">
            <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense" class="w-full h-full object-contain">
        </div>

        <div class="text-center mt-6">
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter leading-none mb-4">Selamat Datang di <span class="text-blue-600">WaterSense</span></h2>
            <p class="text-sm font-bold text-slate-500 leading-relaxed">Sistem informasi hidrologi cerdas untuk keselamatan warga. Pilih cara Anda untuk mulai memantau.</p>
            
            <div class="mt-12 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('login') }}" class="flex flex-col items-center justify-center p-6 rounded-[2rem] bg-slate-900 text-white hover:bg-blue-600 transition-all duration-300 group shadow-xl shadow-slate-900/10">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><i class="fa-solid fa-right-to-bracket text-xl"></i></div>
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-1">Sudah Punya Akun</p>
                        <p class="text-xs font-black uppercase tracking-widest">Masuk</p>
                    </a>

                    <a href="{{ route('register') }}" class="flex flex-col items-center justify-center p-6 rounded-[2rem] bg-white border-2 border-slate-100 text-slate-800 hover:border-blue-500/30 hover:bg-blue-50/30 transition-all duration-300 group shadow-xl shadow-slate-200/20">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center mb-3 group-hover:bg-blue-500 group-hover:text-white transition-all"><i class="fa-solid fa-user-plus text-xl"></i></div>
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-60 mb-1">Pengguna Baru</p>
                        <p class="text-xs font-black uppercase tracking-widest">Daftar</p>
                    </a>
                </div>

                <button onclick="closePortal()" class="flex items-center justify-between w-full p-5 rounded-3xl bg-white border-2 border-slate-100 text-slate-600 hover:border-blue-500/30 hover:bg-blue-50/30 transition-all duration-300 group">
                    <div class="flex items-center text-left">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center mr-4 group-hover:bg-blue-500 group-hover:text-white transition-all"><i class="fa-solid fa-eye text-xl"></i></div>
                        <div><p class="text-[10px] font-black uppercase tracking-widest opacity-60">Akses Cepat</p><p class="text-sm font-black uppercase tracking-widest">Lanjutkan Sebagai Tamu</p></div>
                    </div>
                    <i class="fa-solid fa-chevron-right mr-2 opacity-0 group-hover:opacity-100 transition-all group-hover:translate-x-1"></i>
                </button>
            </div>
            <p class="mt-8 text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] opacity-60">Sistem Pemantauan Citarum Real-Time</p>
        </div>
    </div>
</div>

<style>
    #welcome-portal.active { opacity: 1 !important; visibility: visible !important; }
    #welcome-portal.active #portal-card { transform: scale(1) translateY(0) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const portal = document.getElementById('welcome-portal');
        if (portal) {
            document.body.classList.add('portal-active');
            setTimeout(() => { portal.classList.add('active'); }, 500);
        }
    });

    function closePortal() {
        const portal = document.getElementById('welcome-portal');
        if(!portal) return;
        portal.style.opacity = '0'; portal.style.transition = 'all 0.8s ease';
        document.body.classList.remove('portal-active');
        setTimeout(() => { portal.classList.remove('active'); portal.remove(); }, 800);
    }
</script>
@endguest
