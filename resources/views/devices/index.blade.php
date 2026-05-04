@include('partials.dashboard.head')

<style>
    /* DIRECT CACHE BYPASS CSS - MASTER ASSEMBLY SYSTEM */
    @keyframes revealFromLeft {
        0% { opacity: 0; transform: translateX(-100px); filter: blur(20px); }
        100% { opacity: 1; transform: translateX(0); filter: blur(0); }
    }
    @keyframes revealFromRight {
        0% { opacity: 0; transform: translateX(100px); filter: blur(20px); }
        100% { opacity: 1; transform: translateX(0); filter: blur(0); }
    }
    @keyframes revealFromBottom {
        0% { opacity: 0; transform: translateY(100px); filter: blur(20px); }
        100% { opacity: 1; transform: translateY(0); filter: blur(0); }
    }

    .v-reveal-left, .v-reveal-right, .v-reveal-bottom {
        opacity: 0;
        animation-duration: 1.5s;
        animation-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1);
        animation-fill-mode: forwards;
    }

    body.loaded .v-reveal-left { animation-name: revealFromLeft; }
    body.loaded .v-reveal-right { animation-name: revealFromRight; }
    body.loaded .v-reveal-bottom { animation-name: revealFromBottom; }

    /* Precise Staggered Delays - 0.3s Interval for Master Control */
    body.loaded .delay-1 { animation-delay: 0.2s !important; }
    body.loaded .delay-2 { animation-delay: 0.5s !important; }
    body.loaded .delay-3 { animation-delay: 0.8s !important; }
    body.loaded .delay-4 { animation-delay: 1.1s !important; }
    body.loaded .delay-5 { animation-delay: 1.4s !important; }
    body.loaded .delay-6 { animation-delay: 1.7s !important; }
    body.loaded .delay-7 { animation-delay: 2.0s !important; }
    body.loaded .delay-8 { animation-delay: 2.3s !important; }
</style>

<body class="min-h-screen bg-slate-50 text-slate-700 font-sans selection:bg-cyan-100 selection:text-cyan-900 overflow-x-hidden">
    

    
    <!-- Sophisticated Light Background Elements -->
    <div class="fixed inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-400/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navigation Bar (Light) -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200 shadow-sm">
        <div class="max-w-[1800px] mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                    <i class="fa-solid fa-microchip text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-widest uppercase text-slate-800">Device Command Center</h1>
                    <p class="text-[9px] font-mono text-cyan-600 tracking-[0.3em] uppercase font-bold">Hardware Management Protocol v4.0</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('it.dashboard') }}" class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-slate-200 transition-all flex items-center group">
                    <i class="fa-solid fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Kembali ke NOC
                </a>
                <button onclick="openDeviceModal('add')" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest bg-gradient-to-r from-slate-800 to-slate-900 text-white shadow-xl shadow-slate-200 hover:scale-105 active:scale-95 transition-all flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> Register New Node
                </button>
            </div>
        </div>
    </nav>

    <main class="relative z-10 max-w-[1800px] mx-auto px-6 py-10">
        
        <!-- Header Hero Section (Light) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            <div class="lg:col-span-8 v-reveal-left delay-1">
                <h2 class="text-5xl font-black text-slate-900 tracking-tighter mb-4">Master Node <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-700 font-black">Infrastructure</span></h2>
                <p class="text-slate-500 text-lg max-w-2xl leading-relaxed font-medium">Kelola seluruh armada sensor telemetri Anda dengan presisi tingkat militer. Pantau status konektivitas, kalibrasi sensor, dan konfigurasi GPS secara terpusat.</p>
            </div>
            <div class="lg:col-span-4 grid grid-cols-2 gap-4">
                <div class="v-reveal-bottom delay-2 bg-white border border-slate-200 rounded-[2rem] p-6 flex flex-col justify-center shadow-sm">
                    <span class="text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-2">Total Nodes</span>
                    <div class="text-4xl font-black text-slate-800 font-mono tracking-tighter">{{ count($devices) }}</div>
                </div>
                <div class="v-reveal-bottom delay-3 bg-white border border-slate-200 rounded-[2rem] p-6 flex flex-col justify-center shadow-sm">
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Active Signal</span>
                    <div class="text-4xl font-black text-slate-800 font-mono tracking-tighter">{{ $devices->where('status', 'online')->count() }}</div>
                </div>
            </div>
        </div>

        <!-- Global Flash Messages -->
        @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center text-emerald-600 animate-slide-up shadow-sm">
            <i class="fa-solid fa-circle-check text-xl mr-3"></i>
            <span class="text-sm font-bold uppercase tracking-widest">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Advanced Device Matrix (Light Mode) -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($devices as $index => $device)
            <div class="v-reveal-bottom delay-{{ ($index % 5) + 4 }} group relative bg-white hover:bg-slate-50 border border-slate-200 hover:border-cyan-500/50 rounded-[2.5rem] p-8 transition-all duration-500 shadow-md hover:shadow-2xl hover:shadow-cyan-500/10 overflow-hidden">
                
                <!-- Status Badge -->
                <div class="absolute top-8 right-8 flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full {{ $device->status === 'online' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : ($device->status === 'maintenance' ? 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' : 'bg-slate-300') }} animate-pulse"></span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-600">{{ $device->status }}</span>
                </div>

                <div class="flex items-start justify-between mb-8">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br {{ $device->status === 'online' ? 'from-cyan-500 to-blue-600 shadow-cyan-500/20' : 'from-slate-400 to-slate-500 shadow-slate-300/20' }} flex items-center justify-center shadow-xl">
                        <i class="fa-solid {{ $device->type === 'Ultrasonic Sensor' ? 'fa-wave-square' : 'fa-microchip' }} text-white text-2xl"></i>
                    </div>
                </div>

                <div class="space-y-1 mb-6">
                    <h3 class="text-2xl font-black text-slate-800 group-hover:text-cyan-600 transition-colors tracking-tight">{{ $device->name }}</h3>
                    <div class="flex items-center space-x-2 text-xs font-mono text-slate-400">
                        <i class="fa-solid fa-barcode text-[10px]"></i>
                        <span>SN: {{ $device->serial_number }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Last Latitude</span>
                        <span class="text-xs font-mono text-slate-700 font-bold">{{ $device->latitude ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Last Longitude</span>
                        <span class="text-xs font-mono text-slate-700 font-bold">{{ $device->longitude ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-slate-100 space-x-3">
                    <div class="flex space-x-2">
                        <button onclick="openCalibrationModal({{ json_encode($device) }})" class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-cyan-500 text-slate-400 hover:text-white transition-all flex items-center justify-center border border-slate-200 shadow-sm" title="Calibrate">
                            <i class="fa-solid fa-sliders text-sm"></i>
                        </button>
                        <button onclick="openDeviceModal('edit', {{ json_encode($device) }})" class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-blue-600 text-slate-400 hover:text-white transition-all flex items-center justify-center border border-slate-200 shadow-sm" title="Edit">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                    </div>
                    
                    <form action="{{ route('devices.destroy', $device->id) }}" method="POST" onsubmit="return confirm('DESTROY NODE: Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="h-10 px-4 rounded-xl bg-red-50 hover:bg-red-500 text-red-500 hover:text-white transition-all border border-red-100 text-[10px] font-black uppercase tracking-widest flex items-center shadow-sm">
                            <i class="fa-solid fa-trash-can mr-2"></i> Terminate
                        </button>
                    </form>
                </div>

                <!-- Hover Decorative Element -->
                <div class="absolute bottom-4 right-8 text-[8px] font-mono text-slate-200 uppercase tracking-[0.4em] select-none group-hover:text-cyan-500/10 transition-colors font-black">
                    Hardware ID: {{ substr($device->slug, -8) }}
                </div>
            </div>
            @endforeach

            <!-- Add New Card (Light) -->
            <button onclick="openDeviceModal('add')" class="group relative bg-white hover:bg-slate-50 border-2 border-dashed border-slate-200 hover:border-cyan-500 rounded-[2.5rem] p-8 transition-all duration-500 flex flex-col items-center justify-center space-y-4 min-h-[350px] shadow-sm">
                <div class="w-20 h-20 rounded-full bg-slate-50 group-hover:bg-cyan-500 group-hover:rotate-90 transition-all duration-500 flex items-center justify-center border border-slate-100 shadow-inner">
                    <i class="fa-solid fa-plus text-3xl text-slate-300 group-hover:text-white"></i>
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-widest">Register New Node</h3>
                    <p class="text-slate-400 text-xs mt-2 font-bold tracking-widest uppercase">Expand Infrastructure</p>
                </div>
            </button>
        </div>

    </main>

    <!-- DEVICE MODAL (Add/Edit) - Light Mode Upgrade -->
    <div id="deviceModal" class="fixed inset-0 z-[1000] hidden flex items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_70px_rgba(0,0,0,0.15)] w-full max-w-2xl mx-4 overflow-hidden border border-slate-100 transform transition-all scale-95 opacity-0 duration-300" id="deviceModalContent">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.3em]" id="modalTitle">Register Node</h3>
                    <p class="text-[9px] font-mono text-cyan-600 uppercase mt-1 font-bold">Direct Hardware Configuration</p>
                </div>
                <button onclick="closeDeviceModal()" class="w-10 h-10 rounded-full bg-white hover:bg-red-500 text-slate-400 hover:text-white transition-all flex items-center justify-center shadow-sm border border-slate-100"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="deviceForm" method="POST" action="{{ route('devices.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Node Designation</label>
                        <input type="text" name="name" id="dev_name" required placeholder="e.g. Karawang Barat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sensor Class</label>
                        <input type="text" name="type" id="dev_type" required value="Ultrasonic Sensor" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Serial Authentication</label>
                        <input type="text" name="serial_number" id="dev_sn" required placeholder="NMCU-XXXX-XXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Deployment Sector</label>
                        <input type="text" name="location" id="dev_loc" placeholder="Karawang, Jawa Barat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Geo Latitude</label>
                        <input type="number" step="any" name="latitude" id="dev_lat" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Geo Longitude</label>
                        <input type="number" step="any" name="longitude" id="dev_lng" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                    </div>
                    <div class="space-y-2 md:col-span-2 hidden" id="statusGroup">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Operational Status</label>
                        <select name="status" id="dev_status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                            <option value="online">ONLINE</option>
                            <option value="offline">OFFLINE</option>
                            <option value="maintenance">MAINTENANCE</option>
                        </select>
                    </div>
                </div>
                
                <div class="px-8 py-6 border-t border-slate-100 flex justify-end space-x-4 bg-slate-50">
                    <button type="button" onclick="closeDeviceModal()" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-colors font-bold">Abort</button>
                    <button type="submit" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest bg-slate-800 text-white shadow-xl shadow-slate-200 hover:scale-105 active:scale-95 transition-all flex items-center">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Deploy Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('partials.dashboard.calibration_modal')
    @include('partials.dashboard.scripts')

    <script>
        // Trigger Animations Faster (DOMContentLoaded)
        function triggerReveal() {
            if(!document.body.classList.contains('loaded')) {
                document.body.classList.add('loaded'); 
            }
        }

        window.addEventListener('DOMContentLoaded', triggerReveal);
        window.addEventListener('load', triggerReveal); // Backup
        setTimeout(triggerReveal, 3000); // EMERGENCY FAIL-SAFE (3 Seconds)

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
                title.innerText = 'Register New Node';
                form.action = "{{ route('devices.store') }}";
                method.value = 'POST';
                form.reset();
                statusGroup.classList.add('hidden');
            } else if (mode === 'edit') {
                title.innerText = 'Update Node Configuration';
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
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }
    </script>
</body>
</html>
