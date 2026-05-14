@include('partials.dashboard.head')

<body class="min-h-screen flex items-center justify-center p-4 bg-slate-50 overflow-x-hidden relative">
    <!-- Background -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-400/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-cyan-400/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-2xl relative z-10 v-reveal-item py-10">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white shadow-xl mb-4">
                <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense Logo" class="w-10 h-10 object-contain">
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Join Water<span class="text-blue-600">Sense</span></h1>
            <p class="text-slate-500 font-bold tracking-widest uppercase text-[9px] mt-1">Registrasi Keselamatan Warga</p>
        </div>

        <!-- Registration Wizard -->
        <div class="glass-panel rounded-[2.5rem] border border-white/60 shadow-2xl overflow-hidden">
            <!-- Progress Bar -->
            <div class="flex h-1.5 w-full bg-slate-100">
                <div id="step-progress" class="h-full bg-gradient-to-r from-blue-500 to-cyan-400 transition-all duration-500" style="width: 33.33%"></div>
            </div>

            <form action="{{ route('register') }}" method="POST" id="registerForm" class="p-8 md:p-12">
                @csrf
                
                <!-- STEP 1: Account Info -->
                <div id="step-1" class="step-content space-y-6">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Informasi Akun</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Langkah 1 dari 3</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                            <input type="text" name="name" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Budi Santoso">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp</label>
                            <input type="text" name="phone" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0812xxxx">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Email</label>
                        <input type="email" name="email" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="budi@example.com">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password</label>
                            <input type="password" name="password" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="••••••••">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Safety & Emergency -->
                <div id="step-2" class="step-content space-y-6 hidden">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Detail Keamanan</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Langkah 2 dari 3</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Domisili (Lengkap)</label>
                        <textarea name="address" required rows="3" class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Jl. Raya Karawang No. 123..."></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kontak Darurat (Keluarga/Kerabat)</label>
                        <input type="text" name="emergency_phone" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0857xxxx (Nama - Hubungan)">
                    </div>
                    
                    <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl flex items-start space-x-3">
                        <i class="fa-solid fa-circle-exclamation text-amber-500 mt-1"></i>
                        <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wide leading-relaxed">Pastikan nomor HP dan kontak darurat aktif untuk menerima peringatan dini saat kondisi bahaya.</p>
                    </div>
                </div>

                <!-- STEP 3: Precise Location -->
                <div id="step-3" class="step-content space-y-6 hidden">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight">Titik Lokasi Rumah</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Langkah 3 dari 3 (Final)</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Geser pin ke lokasi rumah Anda</p>
                            <button type="button" onclick="getCurrentLocation()" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-blue-100 transition-all flex items-center">
                                <i class="fa-solid fa-location-crosshairs mr-2"></i> Gunakan GPS
                            </button>
                        </div>
                        
                        <!-- Map Container -->
                        <div id="register-map" class="w-full h-[300px] rounded-3xl border border-slate-200 shadow-inner z-10"></div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Latitude</label>
                                <input type="text" name="latitude" id="lat" readonly required class="w-full bg-slate-100 border-none py-2 px-4 rounded-xl text-xs font-mono font-bold text-slate-600">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Longitude</label>
                                <input type="text" name="longitude" id="lng" readonly required class="w-full bg-slate-100 border-none py-2 px-4 rounded-xl text-xs font-mono font-bold text-slate-600">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wizard Actions -->
                <div class="mt-12 flex justify-between items-center">
                    <button type="button" id="prevBtn" onclick="nextPrev(-1)" class="invisible text-slate-400 hover:text-slate-700 text-[10px] font-black uppercase tracking-widest flex items-center transition-all">
                        <i class="fa-solid fa-chevron-left mr-2"></i> Sebelumnya
                    </button>
                    
                    <button type="button" id="nextBtn" onclick="nextPrev(1)" class="bg-slate-900 text-white px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-blue-600 transition-all flex items-center group">
                        Lanjut <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center mt-8 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
            Sudah terdaftar? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 ml-1">Masuk Sekarang</a>
        </p>
    </div>

    <script>
        let currentStep = 0;
        let map, marker;

        document.addEventListener('DOMContentLoaded', () => {
            showStep(currentStep);
            initMap();
            setTimeout(() => document.body.classList.add('loaded'), 100);
        });

        function initMap() {
            // Default Karawang
            const defaultLat = -6.3012;
            const defaultLng = 107.3054;
            
            map = L.map('register-map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
            
            updateCoords(defaultLat, defaultLng);

            marker.on('dragend', function (e) {
                const pos = marker.getLatLng();
                updateCoords(pos.lat, pos.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoords(e.latlng.lat, e.latlng.lng);
            });
        }

        function updateCoords(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(8);
            document.getElementById('lng').value = lng.toFixed(8);
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 17);
                    marker.setLatLng([lat, lng]);
                    updateCoords(lat, lng);
                }, () => {
                    alert('Gagal mendapatkan lokasi GPS. Silakan tentukan secara manual.');
                });
            }
        }

        function showStep(n) {
            const steps = document.getElementsByClassName("step-content");
            steps[n].classList.remove("hidden");
            
            // UI Update
            document.getElementById("prevBtn").style.visibility = n === 0 ? "hidden" : "visible";
            
            const nextBtn = document.getElementById("nextBtn");
            if (n === (steps.length - 1)) {
                nextBtn.innerHTML = 'Daftar Sekarang <i class="fa-solid fa-check-double ml-2"></i>';
                // Invalidate map size to fix grey tiles issue in hidden divs
                setTimeout(() => map.invalidateSize(), 100);
            } else {
                nextBtn.innerHTML = 'Lanjut <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>';
            }

            document.getElementById("step-progress").style.width = ((n + 1) / steps.length * 100) + "%";
        }

        function nextPrev(n) {
            const steps = document.getElementsByClassName("step-content");
            
            // Validation (Simple)
            if (n === 1 && !validateForm()) return false;

            steps[currentStep].classList.add("hidden");
            currentStep = currentStep + n;

            if (currentStep >= steps.length) {
                document.getElementById("registerForm").submit();
                return false;
            }
            showStep(currentStep);
        }

        function validateForm() {
            // Basic HTML5 validation
            const currentStepEl = document.getElementsByClassName("step-content")[currentStep];
            const inputs = currentStepEl.querySelectorAll("input, textarea");
            let valid = true;
            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    valid = false;
                }
            });
            return valid;
        }
    </script>
</body>
</html>
