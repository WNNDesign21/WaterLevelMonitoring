@include('partials.dashboard.head')

<body class="min-h-screen bg-slate-50 text-slate-700 font-sans selection:bg-cyan-200 selection:text-cyan-900 pb-10 overflow-x-hidden">
    
    <!-- Top Accent Line -->
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-500 z-50 shadow-[0_0_10px_rgba(6,182,212,0.3)]"></div>

    <!-- Main Container -->
    <div class="w-full max-w-[2000px] mx-auto px-4 sm:px-6 lg:px-8 mt-6 relative z-10">
        
        <!-- HEADER: NOC Branding -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b border-slate-200 pb-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-server text-cyan-500 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 tracking-widest uppercase">Pusat Komando NOC</h1>
                    <p class="text-[10px] font-mono text-cyan-600 tracking-[0.3em] uppercase">Analitik Sistem & Telemetri Lanjutan</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                <div class="flex items-center space-x-2 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-mono text-slate-600">WS_TERHUBUNG</span>
                </div>
                <a href="{{ route('user.dashboard') }}" class="text-[10px] font-black text-slate-500 hover:text-cyan-600 transition-colors uppercase tracking-widest border border-slate-200 hover:border-cyan-300 px-4 py-2 rounded-lg bg-white shadow-sm">
                    Tampilan Publik <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- TOP BAR: Micro Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white border border-slate-100 rounded-xl p-3 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-clock text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Waktu Aktif Server</span>
                <span class="text-sm font-mono text-emerald-500" id="it-uptime">99.98%</span>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-3 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-network-wired text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Latensi Reverb</span>
                <span class="text-sm font-mono text-cyan-600"><span id="it-ping">12</span> ms</span>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-3 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-bolt text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tegangan Node</span>
                <span class="text-sm font-mono text-amber-500">5.02 V</span>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-3 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-users text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Koneksi Aktif</span>
                <span class="text-sm font-mono text-blue-500">1,204</span>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-3 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-memory text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Penggunaan Memori</span>
                <span class="text-sm font-mono text-slate-600">42.8 GB / 64.0 GB</span>
            </div>
            <div class="bg-white border border-slate-100 rounded-xl p-3 flex flex-col justify-center relative overflow-hidden group hover:border-slate-300 transition-colors shadow-sm">
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid fa-database text-4xl"></i></div>
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Kueri DB/Detik</span>
                <span class="text-sm font-mono text-slate-600" id="it-qps">450</span>
            </div>
        </div>

        <!-- DENSE GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            
            <!-- LEFT COLUMN (3 Cols) -->
            <div class="lg:col-span-3 flex flex-col space-y-5">
                
                <!-- System Diagnostics -->
                <div class="bg-white border border-slate-100 rounded-[1.5rem] p-5 relative overflow-hidden shadow-lg">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-2xl"></div>
                    <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-5 flex items-center border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-microchip mr-2 text-cyan-500"></i> Status Perangkat Keras
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between text-[10px] mb-1.5 font-mono">
                                <span class="text-slate-500">SUHU_CPU</span>
                                <span class="text-amber-500 font-bold">48.5°C</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-400 to-amber-300 h-1.5 rounded-full" style="width: 48%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[10px] mb-1.5 font-mono">
                                <span class="text-slate-500">RUGI_PAKET</span>
                                <span class="text-emerald-500 font-bold">0.00%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-400 to-emerald-300 h-1.5 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[10px] mb-1.5 font-mono">
                                <span class="text-slate-500">KUAT_SINYAL</span>
                                <span class="text-cyan-500 font-bold">-64 dBm</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-cyan-500 to-cyan-400 h-1.5 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terminal Log -->
                <div class="bg-slate-100 border border-slate-200 rounded-[1.5rem] p-4 flex-1 flex flex-col min-h-[300px] shadow-inner relative">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-3 shrink-0">
                        <h3 class="text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center">
                            <i class="fa-solid fa-terminal mr-2 text-slate-400"></i> Log Sistem
                        </h3>
                        <span class="text-[8px] font-mono text-emerald-600 bg-emerald-500/10 px-1.5 py-0.5 rounded animate-pulse">MEMANTAU...</span>
                    </div>
                    <div id="it-terminal" class="flex-1 font-mono text-[9px] leading-relaxed text-slate-500 overflow-y-hidden relative">
                        <div class="absolute bottom-0 w-full space-y-1.5">
                            <div class="text-slate-500">[SYS] Memulai proses jabat tangan...</div>
                            <div class="text-emerald-600">[OK] Saluran aman berhasil dibuat.</div>
                            <div class="text-cyan-600">[ALIRAN] Menunggu telemetri masuk.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CENTER COLUMN (6 Cols) -->
            <div class="lg:col-span-6 flex flex-col space-y-5">
                
                <!-- Raw Telemetry KPI -->
                <div class="bg-white border border-slate-100 rounded-[2rem] p-6 relative overflow-hidden shadow-xl flex flex-col justify-center">
                    <div class="absolute -right-10 -bottom-10 opacity-5 pointer-events-none">
                        <i class="fa-solid fa-water text-9xl"></i>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-6 relative z-10">
                        <div class="col-span-3 lg:col-span-1 border-b lg:border-b-0 lg:border-r border-slate-100 pb-4 lg:pb-0">
                            <h3 class="text-[9px] text-slate-400 font-black mb-2 uppercase tracking-[0.2em]">Jarak Sensor</h3>
                            <div class="flex items-baseline">
                                <span id="current-distance" class="text-4xl font-black text-slate-700 font-mono odometer">--</span>
                                <span class="text-sm text-slate-400 ml-2 font-mono">cm</span>
                            </div>
                        </div>
                        <div class="col-span-3 lg:col-span-1 border-b lg:border-b-0 lg:border-r border-slate-100 pb-4 lg:pb-0">
                            <h3 class="text-[9px] text-slate-400 font-black mb-2 uppercase tracking-[0.2em]">Kecepatan Aliran</h3>
                            <div class="flex items-baseline">
                                <span id="flow-velocity" class="text-4xl font-black text-cyan-500 font-mono odometer">0.00</span>
                                <span class="text-sm text-slate-400 ml-2 font-mono">cm/s</span>
                            </div>
                        </div>
                        <div class="col-span-3 lg:col-span-1">
                            <h3 class="text-[9px] text-slate-400 font-black mb-2 uppercase tracking-[0.2em]">Tinggi Permukaan Air</h3>
                            <div class="flex items-baseline">
                                <span id="water-level" class="text-5xl font-black text-slate-800 font-mono odometer">--</span>
                                <span class="text-sm text-blue-500 ml-2 font-bold font-mono">MDPL</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sparkline inside KPI -->
                    <div class="mt-6 h-20 w-full relative z-10">
                        <canvas id="tma-sparkline"></canvas>
                    </div>
                </div>

                <!-- Wireframe River Tank -->
                <div class="bg-white border border-slate-100 rounded-[2rem] p-3 flex-1 relative min-h-[350px] shadow-inner group">
                    <div class="absolute top-5 left-5 z-[100] bg-white border border-slate-200 px-3 py-1.5 rounded-lg flex items-center space-x-2 shadow-sm">
                        <i class="fa-solid fa-layer-group text-slate-400 text-[10px]"></i>
                        <span class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Simulasi Visual</span>
                    </div>
                    
                    <!-- X-Ray Glass Tank -->
                    <div class="glass-tank-container relative w-full h-full rounded-[1.5rem] bg-slate-50 border border-slate-200 overflow-hidden shadow-[inset_0_0_30px_rgba(0,0,0,0.05)]">
                        
                        <!-- Water Layer -->
                        <div class="liquid-water transition-all duration-700 ease-out" id="river-water" style="height: 0%;">
                            <div class="liquid-water-particles"></div>
                            <!-- Dynamic Percent Label -->
                            <div id="water-percent-container" class="absolute inset-x-0 top-1/2 -translate-y-1/2 text-center transition-all duration-700 pointer-events-none z-20">
                                <div id="water-percent" class="text-6xl font-black text-white/20 uppercase tracking-tighter mix-blend-overlay font-mono">0%</div>
                            </div>
                        </div>

                        <!-- Foreground Overlays (High Contrast) -->
                        <div class="absolute inset-0 z-40 pointer-events-none">
                            @for($i = 0; $i <= 600; $i += 50)
                                @php 
                                    $bottomPercent = ($i / 600) * 100; 
                                    $tmaValue = 8.00 + ($i / 100);
                                @endphp
                                @if($i % 100 == 0)
                                    <div class="absolute right-0 left-0 h-px bg-slate-900/[0.08]" style="bottom: {{ $bottomPercent }}%"></div>
                                    <div class="depth-tick !w-12 !bg-slate-900" style="bottom: {{ $bottomPercent }}%"></div>
                                    <div class="depth-value !text-[10px]" style="bottom: {{ $bottomPercent }}%; transform: translateY(50%)">{{ number_format($tmaValue, 2) }}</div>
                                @else
                                    <div class="depth-tick !w-4" style="bottom: {{ $bottomPercent }}%"></div>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (3 Cols) -->
            <div class="lg:col-span-3 flex flex-col space-y-5">
                
                <!-- IT Sentinel Map -->
                <div class="bg-white border border-slate-100 rounded-[1.5rem] p-2 relative h-[250px] flex flex-col shadow-lg">
                    <div class="absolute top-4 left-4 z-[400] bg-white/90 backdrop-blur-sm border border-slate-200 px-3 py-1.5 rounded-lg flex items-center space-x-2 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                        <span class="text-[9px] font-mono text-slate-600 uppercase tracking-widest">UPLINK_SATELIT</span>
                    </div>
                    <div id="it-sentinel-map" class="w-full flex-1 rounded-[1rem] z-0 bg-slate-100"></div>
                </div>

                <!-- AI ETA Predictions -->
                <div class="bg-white border border-slate-100 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500" id="it-eta-card">
                    <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-brain text-9xl text-slate-300"></i>
                    </div>
                    <h3 class="text-[9px] text-slate-500 font-black mb-5 uppercase tracking-[0.2em] flex items-center border-b border-slate-100 pb-2">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-blue-500"></i> Matriks AI Prediktif
                    </h3>
                    
                    <div class="space-y-6 relative z-10">
                        <div>
                            <span class="text-[8px] font-mono text-slate-400 uppercase block mb-1">Estimasi Waktu Meluap</span>
                            <div class="text-3xl font-black text-emerald-500 font-mono" id="eta-overflow">STABLE</div>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-[8px] font-mono text-slate-400 uppercase block mb-2">Tingkat Kepercayaan</span>
                            <div class="flex items-center space-x-3">
                                <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-200">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-400 h-2 rounded-full" style="width: 94%"></div>
                                </div>
                                <span class="text-[10px] font-mono text-blue-500 font-bold">94.2%</span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="text-[8px] font-mono text-slate-400 uppercase block mb-2">Model Regresi</span>
                            <div class="text-[9px] font-mono text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-200">
                                Y = &beta;₀ + &beta;₁X + &epsilon;<br>
                                <span class="text-slate-400">Node Pemrosesan:</span> GPU_04<br>
                                <span class="text-slate-400">Status:</span> <span class="text-emerald-500">OPTIMAL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- DEVICE MANAGEMENT SECTION -->
        <div class="mt-8 bg-white border border-slate-100 rounded-[1.5rem] p-6 shadow-lg relative z-10">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-black text-slate-800 uppercase tracking-widest flex items-center">
                        <i class="fa-solid fa-microchip text-cyan-500 mr-2"></i> Manajemen Perangkat
                    </h2>
                    <p class="text-[10px] text-slate-400 font-mono mt-1">SISTEM KONTROL PERANGKAT & SENSOR (CRUD)</p>
                </div>
                <button onclick="openDeviceModal('add')" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors shadow-sm flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Perangkat
                </button>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mb-4 bg-emerald-50 text-emerald-600 px-4 py-3 rounded-xl text-sm font-bold border border-emerald-100 flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-600 px-4 py-3 rounded-xl text-sm font-bold border border-red-100">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] text-slate-400 uppercase tracking-widest font-black border-b border-slate-100">
                            <th class="p-4">Nama Perangkat</th>
                            <th class="p-4">Tipe</th>
                            <th class="p-4">Serial Number</th>
                            <th class="p-4">Lokasi</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium text-slate-600">
                        @forelse($allDevices ?? [] as $dev)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $dev->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $dev->slug }}</div>
                            </td>
                            <td class="p-4">{{ $dev->type }}</td>
                            <td class="p-4 font-mono text-xs">{{ $dev->serial_number }}</td>
                            <td class="p-4 text-xs">{{ $dev->location ?? '-' }}</td>
                            <td class="p-4">
                                @if($dev->status == 'online')
                                    <span class="bg-emerald-100 text-emerald-600 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest">Online</span>
                                @elseif($dev->status == 'maintenance')
                                    <span class="bg-amber-100 text-amber-600 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest">MTNC</span>
                                @else
                                    <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest">Offline</span>
                                @endif
                            </td>
                            <td class="p-4 text-center flex justify-center space-x-2">
                                <a href="{{ route('user.dashboard.device', $dev->slug) }}" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white transition-colors flex items-center justify-center" title="Lihat Dasbor">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <button onclick="openDeviceModal('edit', {{ json_encode($dev) }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-colors flex items-center justify-center" title="Edit Perangkat">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('devices.destroy', $dev->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus perangkat ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-sm">Tidak ada perangkat ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- DEVICE MODAL (Add/Edit) -->
    <div id="deviceModal" class="fixed inset-0 z-[1000] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-[1.5rem] shadow-2xl w-full max-w-2xl mx-4 overflow-hidden border border-slate-100 transform transition-all scale-95 opacity-0 duration-300" id="deviceModalContent">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest" id="modalTitle">Tambah Perangkat</h3>
                <button onclick="closeDeviceModal()" class="text-slate-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="deviceForm" method="POST" action="{{ route('devices.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Nama Perangkat</label>
                        <input type="text" name="name" id="dev_name" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tipe Perangkat</label>
                        <input type="text" name="type" id="dev_type" required value="Ultrasonic Sensor" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Serial Number</label>
                        <input type="text" name="serial_number" id="dev_sn" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono text-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Lokasi</label>
                        <input type="text" name="location" id="dev_loc" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Latitude</label>
                        <input type="number" step="any" name="latitude" id="dev_lat" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Longitude</label>
                        <input type="number" step="any" name="longitude" id="dev_lng" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-1 md:col-span-2 hidden" id="statusGroup">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</label>
                        <select name="status" id="dev_status" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 flex justify-end space-x-3 bg-slate-50">
                    <button type="button" onclick="closeDeviceModal()" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest bg-cyan-500 text-white hover:bg-cyan-600 transition-colors shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- System Scripts -->
    @include('partials.dashboard.scripts')

    <!-- IT Specific Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // --- 1. Terminal Log Simulator ---
            const terminal = document.querySelector('#it-terminal > div');
            const logs = [
                "[NET] Menerima paket dari 192.168.1.104 ukuran=42B",
                "[SYS] Pembersihan memori berjalan. Bebas 12MB.",
                "[DB] Waktu eksekusi kueri: 1.2ms.",
                "[SENSOR] Ping ultrasonik dikonfirmasi dalam 0.4d.",
                "[AI] Menghitung ulang varians lintasan prediksi...",
                "[WS] Menyiarkan 'SensorDataUpdated' -> 1204 klien.",
                "[NODE] Tegangan stabil pada 5.02V.",
                "[KEAM] Firewall menahan pemindaian port dari 45.x.x.x.",
                "[GIS] Sinkronisasi orbit satelit Sentinel-2A selesai."
            ];
            
            setInterval(() => {
                const newLog = document.createElement('div');
                newLog.className = 'text-slate-500';
                const randLog = logs[Math.floor(Math.random() * logs.length)];
                const time = new Date().toISOString().split('T')[1].substring(0, 8);
                newLog.textContent = `[${time}] ${randLog}`;
                
                terminal.appendChild(newLog);
                if (terminal.children.length > 15) {
                    terminal.removeChild(terminal.firstChild);
                }
            }, 2000);

            // --- 2. Micro Metrics Randomizer ---
            setInterval(() => {
                document.getElementById('it-ping').textContent = Math.floor(Math.random() * 5) + 10; // 10-14ms
                document.getElementById('it-qps').textContent = Math.floor(Math.random() * 100) + 400; // 400-500
            }, 1000);

            // --- 3. IT Sentinel Map (Light Mode) ---
            const itMapContainer = document.getElementById('it-sentinel-map');
            if (itMapContainer && typeof L !== 'undefined') {
                setTimeout(() => {
                    const itMap = L.map('it-sentinel-map', {
                        center: [{{ env('DEFAULT_LATITUDE', '-6.295') }}, {{ env('DEFAULT_LONGITUDE', '107.31') }}],
                        zoom: 14, 
                        zoomControl: false, 
                        attributionControl: false
                    });
                    
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19
                    }).addTo(itMap);

                    const customIcon = L.divIcon({
                        className: 'custom-marker',
                        html: `<div style="width: 12px; height: 12px; background-color: #ef4444; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 10px rgba(239,68,68,0.4);"></div>`,
                        iconSize: [12, 12],
                        iconAnchor: [6, 6]
                    });

                    const marker = L.marker([{{ env('DEFAULT_LATITUDE', '-6.295') }}, {{ env('DEFAULT_LONGITUDE', '107.31') }}], {icon: customIcon}).addTo(itMap);
                }, 500);
            }

            // --- 4. IT ETA Card Observer (Light Theme Reactive) ---
            const etaOverflow = document.getElementById('eta-overflow');
            const etaCard = document.getElementById('it-eta-card');
            
            if(etaOverflow && etaCard) {
                const observer = new MutationObserver(() => {
                    const text = etaOverflow.textContent;
                    if(text === 'STABLE' || text.includes('>1 Jam')) {
                        etaCard.className = 'bg-white border border-slate-100 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500';
                        etaOverflow.className = 'text-3xl font-black text-emerald-500 font-mono';
                    } else if(text === 'IMMINENT') {
                        etaCard.className = 'bg-red-50 border border-red-200 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500 animate-pulse';
                        etaOverflow.className = 'text-3xl font-black text-red-600 font-mono drop-shadow-sm';
                    } else {
                        etaCard.className = 'bg-orange-50 border border-orange-200 rounded-[1.5rem] p-5 flex-1 relative overflow-hidden group shadow-lg transition-colors duration-500';
                        etaOverflow.className = 'text-3xl font-black text-orange-500 font-mono drop-shadow-sm';
                    }
                });
                observer.observe(etaOverflow, { childList: true, characterData: true, subtree: true });
            }
        });

        // --- Device Management Logic ---
        function openDeviceModal(mode, device = null) {
            const modal = document.getElementById('deviceModal');
            const content = document.getElementById('deviceModalContent');
            const form = document.getElementById('deviceForm');
            const title = document.getElementById('modalTitle');
            const method = document.getElementById('formMethod');
            const statusGroup = document.getElementById('statusGroup');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);

            if (mode === 'add') {
                title.innerText = 'Tambah Perangkat';
                form.action = "{{ route('devices.store') }}";
                method.value = 'POST';
                form.reset();
                statusGroup.classList.add('hidden');
            } else if (mode === 'edit') {
                title.innerText = 'Edit Perangkat';
                form.action = "/it/devices/" + device.id;
                method.value = 'PUT';
                statusGroup.classList.remove('hidden');
                
                document.getElementById('dev_name').value = device.name || '';
                document.getElementById('dev_type').value = device.type || '';
                document.getElementById('dev_sn').value = device.serial_number || '';
                document.getElementById('dev_loc').value = device.location || '';
                document.getElementById('dev_lat').value = device.latitude || '';
                document.getElementById('dev_lng').value = device.longitude || '';
                if(document.getElementById('dev_status')) {
                    document.getElementById('dev_status').value = device.status || 'offline';
                }
            }
        }

        function closeDeviceModal() {
            const modal = document.getElementById('deviceModal');
            const content = document.getElementById('deviceModalContent');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>
