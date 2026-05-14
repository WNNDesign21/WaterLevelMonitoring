@include('partials.dashboard.head')

<body class="min-h-screen flex items-center justify-center p-4 bg-slate-50 overflow-x-hidden relative">
    <div class="w-full max-w-2xl relative z-10 v-reveal-item py-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white shadow-xl mb-4">
                <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" alt="WaterSense Logo" class="w-10 h-10 object-contain">
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tighter">Satu Langkah Lagi!</h1>
            <p class="text-slate-500 font-bold tracking-widest uppercase text-[9px] mt-1">Lengkapi data keselamatan Anda</p>
        </div>

        <div class="glass-panel rounded-[2.5rem] border border-white/60 shadow-2xl p-8 md:p-12 overflow-hidden">
            <form action="{{ route('register.complete') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp</label>
                        <input type="text" name="phone" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0812xxxx">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Domisili</label>
                        <textarea name="address" required rows="3" class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Jl. Raya Karawang..."></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kontak Darurat</label>
                        <input type="text" name="emergency_phone" required class="w-full bg-slate-50/50 border border-slate-200 py-4 px-6 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="0857xxxx (Nama - Hubungan)">
                    </div>

                    <div class="space-y-4">
                         <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Titik Lokasi Rumah</label>
                         <div id="register-map" class="w-full h-[250px] rounded-3xl border border-slate-200 shadow-inner z-10"></div>
                         <input type="hidden" name="latitude" id="lat" required>
                         <input type="hidden" name="longitude" id="lng" required>
                         <button type="button" onclick="getCurrentLocation()" class="text-blue-600 text-[10px] font-black uppercase tracking-widest hover:underline flex items-center">
                            <i class="fa-solid fa-location-crosshairs mr-2"></i> Gunakan GPS Saya
                         </button>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-blue-600 transition-all">
                        Simpan & Masuk Dashboard <i class="fa-solid fa-check ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let map, marker;
        document.addEventListener('DOMContentLoaded', () => {
            const defaultLat = -6.3012;
            const defaultLng = 107.3054;
            map = L.map('register-map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
            updateCoords(defaultLat, defaultLng);
            marker.on('dragend', e => {
                const pos = marker.getLatLng();
                updateCoords(pos.lat, pos.lng);
            });
            map.on('click', e => {
                marker.setLatLng(e.latlng);
                updateCoords(e.latlng.lat, e.latlng.lng);
            });
        });
        function updateCoords(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        }
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 17);
                    marker.setLatLng([lat, lng]);
                    updateCoords(lat, lng);
                });
            }
        }

        // Reveal Script
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.add('loaded');
            }, 100);
        });
    </script>
</body>
</html>
