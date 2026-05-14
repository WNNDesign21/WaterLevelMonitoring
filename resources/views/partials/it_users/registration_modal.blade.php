<!-- User Registration Modal -->
<div id="userModal" class="fixed inset-0 z-[1000] invisible opacity-0 transition-all duration-500 overflow-y-auto">
    <div class="min-h-screen px-4 text-center flex items-center justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleUserModal()"></div>
        <div class="inline-block w-full max-w-xl p-5 md:p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white/90 backdrop-blur-2xl shadow-[0_40px_80px_rgba(0,0,0,0.2)] rounded-[2rem] md:rounded-[2.5rem] border border-white relative z-10 scale-90 opacity-0 duration-500" id="userModalContent">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">User <span class="text-blue-600">Registration</span></h2>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Daftarkan User Baru ke Sistem</p>
                </div>
                <button onclick="toggleUserModal()" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all"><i class="fa-solid fa-xmark"></i></button>
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
                    <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] hover:shadow-xl hover:shadow-blue-500/20 hover:-translate-y-0.5 transition-all duration-300 shadow-lg">Daftarkan User Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
