<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const elCurrent = document.getElementById('rt-current');
        const elVoltage = document.getElementById('rt-voltage');
        const elLatency = document.getElementById('rt-latency');
        const elFlow = document.getElementById('rt-flow');
        const elStatusText = document.getElementById('rt-status-text');
        const elStatusDot = document.getElementById('rt-status-dot');
        const elLastSeen = document.getElementById('last-seen-text');

        // Oscilloscope Setup
        const canvas = document.getElementById('telemetryCanvas');
        const ctx = canvas.getContext('2d');
        let dataPoints = Array(100).fill(0);
        
        function resize() {
            canvas.width = canvas.parentElement.clientWidth;
            canvas.height = canvas.parentElement.clientHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.beginPath();
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 2;
            ctx.shadowBlur = 10;
            ctx.shadowColor = '#3b82f6';

            const step = canvas.width / (dataPoints.length - 1);
            for(let i = 0; i < dataPoints.length; i++) {
                const x = i * step;
                const v = dataPoints[i]; 
                const y = canvas.height - (v * (canvas.height * 0.8));
                if(i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            }
            ctx.stroke();
            requestAnimationFrame(draw);
        }
        draw();

        // Echo Setup
        const echo = new Echo({
            broadcaster: 'reverb',
            key: "{{ env('REVERB_APP_KEY') }}",
            wsHost: window.location.hostname,
            wsPort: {{ env('REVERB_PORT', 8081) }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });

        console.log("[DEVICE ECHO] Connecting with key:", "{{ env('REVERB_APP_KEY') }}");

        let lastDataTime = Date.now();
        let statusTimeout = null;

        echo.channel('sensor-data')
            .listen('.sensor.updated', (e) => {
                console.log("[DEVICE ECHO] Data Received:", e);
                const now = Date.now();
                const latency = now - lastDataTime;
                lastDataTime = now;

                // Map real sensor data to health analogs
                const dist = e.sensorData.distance;
                const simulatedA = (42 + (dist % 5)).toFixed(1); // Slightly reactive current
                const simulatedV = (5.0 + (Math.random() * 0.04 - 0.02)).toFixed(2);
                const simulatedLat = latency > 3000 ? '5.2ms' : (latency/100).toFixed(1) + 'ms';
                const simulatedKbps = (0.8 + Math.random() * 0.1).toFixed(2);

                if(elCurrent) {
                    elCurrent.textContent = simulatedA;
                    elVoltage.textContent = simulatedV;
                    elLatency.textContent = simulatedLat;
                    elFlow.textContent = simulatedKbps + ' kbps';
                    if(elLastSeen) elLastSeen.textContent = 'Baru saja';
                    
                    dataPoints.push(parseFloat(simulatedA) / 60); 
                    dataPoints.shift();

                    // Update component metrics if they exist
                    const elMegaV = document.getElementById('metric-comp-mega-voltage');
                    if(elMegaV) elMegaV.textContent = simulatedV + 'V';
                    const elMegaA = document.getElementById('metric-comp-mega-current');
                    if(elMegaA) elMegaA.textContent = simulatedA + 'mA';
                    
                    // Update sensor component metrics
                    const elJsnSignal = document.getElementById('metric-comp-jsn-signal_strength');
                    if(elJsnSignal) elJsnSignal.textContent = (95 + Math.random() * 4).toFixed(0) + '%';
                    const elJsnResp = document.getElementById('metric-comp-jsn-response_time');
                    if(elJsnResp) elJsnResp.textContent = (10 + Math.random() * 5).toFixed(0) + 'ms';
                    
                    updateUIStatus('online');
                }
            })
            .listen('.device.status.updated', (e) => {
                console.log("[DEVICE ECHO] Status Change:", e);
                if(e.device.slug === '{{ $device->slug }}') {
                    updateUIStatus(e.device.status);
                }
            });

        function updateUIStatus(status) {
            if (status === 'online') {
                elStatusDot.className = 'w-3 h-3 rounded-full bg-emerald-500 animate-pulse';
                elStatusText.textContent = 'ONLINE';
                refreshHeartbeat();
            } else {
                elStatusDot.className = 'w-3 h-3 rounded-full bg-slate-400';
                elStatusText.textContent = status.toUpperCase();
                if (statusTimeout) clearTimeout(statusTimeout);
            }
        }

        function refreshHeartbeat() {
            if (statusTimeout) clearTimeout(statusTimeout);
            if(elStatusText.textContent === 'OFFLINE') updateUIStatus('online');
            statusTimeout = setTimeout(() => updateUIStatus('offline'), 10000);
        }

        // --- Interactive Map Logic ---
        const devLat = {{ $device->latitude ?? -6.2088 }};
        const devLng = {{ $device->longitude ?? 106.8456 }};
        
        const iMap = L.map('interactive-map', {
            center: [devLat, devLng],
            zoom: 15,
            zoomControl: true,
            attributionControl: false
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(iMap);

        const iIcon = L.divIcon({
            className: 'custom-pin',
            html: '<div class="pin-marker"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const iMarker = L.marker([devLat, devLng], { icon: iIcon, draggable: true }).addTo(iMap);
        const elCoords = document.getElementById('current-coords-display');
        
        async function resolveAddress(lat, lng) {
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`;
                const res = await fetch(url);
                const data = await res.json();
                if(data.display_name) {
                    const addrText = document.getElementById('device-address-display');
                    if(addrText) addrText.textContent = data.display_name.toUpperCase();
                }
            } catch(e) { console.error("Geo error:", e); }
        }
        resolveAddress(devLat, devLng);

        iMarker.on('drag', function(e) {
            const pos = e.target.getLatLng();
            elCoords.textContent = `${pos.lat.toFixed(7)}, ${pos.lng.toFixed(7)}`;
            resolveAddress(pos.lat, pos.lng);
        });

        document.getElementById('btn-save-location').addEventListener('click', async function() {
            const pos = iMarker.getLatLng();
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin mr-2"></i> SAVING...';

            try {
                const response = await fetch("{{ route('devices.update_location', $device->slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ latitude: pos.lat, longitude: pos.lng })
                });

                const data = await response.json();
                if(data.success) {
                    btn.classList.replace('bg-blue-600', 'bg-emerald-500');
                    btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i> LOKASI TERSIMPAN!';
                    setTimeout(() => {
                        btn.classList.replace('bg-emerald-500', 'bg-blue-600');
                        btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-2"></i> SIMPAN LOKASI';
                        btn.disabled = false;
                    }, 2000);
                } else { throw new Error(data.message); }
            } catch (error) {
                btn.classList.replace('bg-blue-600', 'bg-rose-500');
                btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-2"></i> FAILED';
                setTimeout(() => {
                    btn.classList.replace('bg-rose-500', 'bg-blue-600');
                    btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-2"></i> SIMPAN LOKASI';
                    btn.disabled = false;
                }, 3000);
            }
        });
    });
</script>
