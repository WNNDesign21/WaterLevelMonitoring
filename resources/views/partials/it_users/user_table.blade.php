<!-- MAIN USER MATRIX -->
<div class="lg:col-span-9 space-y-6">
    <div class="v-reveal-left" style="--delay: 1.0s">
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
                                <div class="text-sm font-bold text-slate-600 flex items-center"><i class="fa-solid fa-envelope mr-3 text-slate-300"></i> {{ $user->email }}</div>
                                <div class="text-[10px] font-black text-slate-400 mt-2 flex items-center"><i class="fa-solid fa-phone mr-3 text-slate-300"></i> {{ $user->phone ?? '---_---_----' }}</div>
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
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-red-100 shadow-sm group/btn"><i class="fa-solid fa-trash-can text-xs group-hover/btn:scale-110 transition-transform"></i></button>
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
    <!-- Pagination -->
    <div class="v-reveal-item delay-2 glass-panel px-8 py-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between">
        <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Matrix_Range: {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}</span>
        <div>{{ $users->links() }}</div>
    </div>
</div>
