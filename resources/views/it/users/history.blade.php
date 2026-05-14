@include('partials.dashboard.head')

<body class="min-h-screen bg-slate-50 font-sans antialiased selection:bg-blue-200 selection:text-blue-900 relative pb-10">
    <!-- Top Accent Line -->
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-500 z-50 shadow-[0_0_10px_rgba(59,130,246,0.3)]"></div>

    <style>
        @keyframes revealFadeUp {
            0% { opacity: 0; transform: translateY(30px); filter: blur(10px); }
            100% { opacity: 1; transform: translateY(0); filter: blur(0); }
        }
        .v-reveal-item { opacity: 0; }
        body.loaded .v-reveal-item { 
            animation: revealFadeUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; 
        }
    </style>

    <div class="max-w-[2000px] mx-auto px-4 md:px-8 pt-12">
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-3 mb-8 v-reveal-item">
            <a href="{{ route('it.users.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-colors">User Management</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Audit_Log_History</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-12 v-reveal-item">
            <div>
                <h1 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tighter leading-none mb-3">
                    Audit <span class="text-blue-600">Trail</span> Archive
                </h1>
                <p class="text-slate-400 font-bold tracking-[0.3em] uppercase text-[10px] flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-3 text-blue-500"></i> SYSTEM_CHRONOLOGY_V7.2
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="glass-panel px-6 py-4 rounded-2xl bg-white/80 border border-slate-100 shadow-sm flex flex-col items-center min-w-[150px]">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total_Records</span>
                    <span class="text-xl font-black text-slate-800 font-mono">{{ $logs->total() }}</span>
                </div>
                <a href="{{ route('it.users.index') }}" class="px-8 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-xl shadow-slate-900/10 flex items-center justify-center border border-slate-700">
                    <i class="fa-solid fa-arrow-left mr-3"></i> Back_to_Control
                </a>
            </div>
        </div>

        <!-- Main Table Matrix -->
        <div class="v-reveal-item delay-1">
            <div class="glass-panel rounded-[3rem] bg-white border border-slate-100 shadow-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Timestamp</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">User_Identity</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Event_Type</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Chronology</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Uplink_IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($logs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="text-[10px] font-mono font-bold text-slate-800">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                                    <div class="text-[8px] font-black text-slate-400 uppercase mt-1">{{ $log->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-[10px] font-black border border-slate-200">
                                            {{ $log->user ? substr($log->user->name, 0, 1) : 'G' }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $log->user ? $log->user->name : 'Guest/Visitor' }}</div>
                                            <div class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $log->user ? $log->user->role : 'External_Access' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $bg = 'bg-slate-100 text-slate-600';
                                        if(str_contains($log->event_type, 'success')) $bg = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                        if(str_contains($log->event_type, 'failed')) $bg = 'bg-red-50 text-red-600 border-red-100';
                                        if(str_contains($log->event_type, 'sso')) $bg = 'bg-blue-50 text-blue-600 border-blue-100';
                                    @endphp
                                    <span class="px-3 py-1.5 rounded-lg border {{ $bg }} text-[8px] font-black uppercase tracking-widest inline-block">
                                        {{ str_replace('_', ' ', $log->event_type) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-[10px] font-bold text-slate-600 leading-relaxed max-w-md italic">"{{ $log->description }}"</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-[10px] font-mono font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100/50 inline-block">
                                        {{ $log->ip_address }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 glass-panel px-8 py-6 rounded-[2rem] bg-white border border-slate-100 shadow-sm flex items-center justify-between v-reveal-item delay-2">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Chronology_Range: {{ $logs->firstItem() }} - {{ $logs->lastItem() }}</span>
                <div>{{ $logs->links() }}</div>
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
