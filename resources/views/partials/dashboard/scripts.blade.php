<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // --- Configuration ---
        const SENSOR_ELEVATION_MDPL = 14.00; 
        const SIAGA_1_TMA = 13.00; // mdpl (1 meter dari sensor)
        const SIAGA_2_TMA = 12.80; // mdpl (1.2 meter dari sensor)
        const SIAGA_3_TMA = 12.50; // mdpl (1.5 meter dari sensor)
        const MAX_SAMPLES = 40;   
        
        // --- State ---
        let chartData = Array(MAX_SAMPLES).fill(0);
        let chartLabels = Array(MAX_SAMPLES).fill('');
        let previousDepth = 0;
        let lastRainIntensity = 0;
        let currentLat = {{ $primaryDevice->latitude ?? -6.2088 }};
        let currentLng = {{ $primaryDevice->longitude ?? 106.8456 }};
        
        let depthBuffer = []; 
        let lastVoiceTime = 0;
        let trendData24h = []; 
        let lastTrendUpdate = 0;
        let maxPeak = 0;
        let minBase = 9999;

        // --- Mock Historical Seed (For Immediate Visual Density) ---
        for(let i=0; i<24; i++) {
            trendData24h.push({
                t: Date.now() - (24-i)*3600000,
                d: 8.5 + Math.random() * 2 // TMA dummy
            });
        }
        
        // --- DOM Selectors (Multi-instance safe helpers) ---
        const updateText = (selector, text) => document.querySelectorAll(selector).forEach(el => el.textContent = text);
        const updateHtml = (selector, html) => document.querySelectorAll(selector).forEach(el => el.innerHTML = html);

        // --- Echo / WebSocket Initialization ---
        let echoInstance = null;
        if(typeof Echo !== 'undefined') {
            echoInstance = new Echo({
                broadcaster: 'reverb',
                key: '{{ env('REVERB_APP_KEY') }}',
                wsHost: window.location.hostname || '127.0.0.1',
                wsPort: {{ env('REVERB_PORT', 8081) }},
                forceTLS: false,
                encrypted: false,
                enabledTransports: ['ws', 'wss'],
            });
            
            console.log('SENTINEL Echo Initializing...');

            // Connection Status UI
            echoInstance.connector.pusher.connection.bind('connected', () => {
                console.log('SENTINEL CONNECTED to Reverb');
                updateText('#connection-text', 'SENTINEL CONNECTED');
                document.querySelectorAll('#connection-dot').forEach(el => el.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse');
            });

            echoInstance.connector.pusher.connection.bind('error', (err) => {
                console.error('SENTINEL CONNECTION ERROR:', err);
                updateText('#connection-text', 'CONNECTION ERROR');
            });
            
            echoInstance.connector.pusher.connection.bind('disconnected', () => {
                console.warn('SENTINEL DISCONNECTED');
                updateText('#connection-text', 'SENTINEL OFFLINE');
                document.querySelectorAll('#connection-dot').forEach(el => el.className = 'w-2 h-2 rounded-full bg-red-500');
            });

            // Listen for Sensor Data
            console.log('SENTINEL Subscribing to sensor-data channel...');
            echoInstance.channel('sensor-data')
                .subscribed(() => {
                    console.log('SENTINEL SUCCESSFULLY SUBSCRIBED to sensor-data');
                })
                .listen('.sensor.updated', (e) => {
                    console.log('Telemetry Received:', e);
                    if(e.sensorData) {
                        updateDashboard(e.sensorData.distance, e.sensorData.valid_count);
                        triggerMapPulse(e.sensorData.distance);
                    }
                })
                .listen('.device.location.updated', (e) => {
                    if(e.device.slug === 'cybernova-s400-primary') {
                        const newLat = parseFloat(e.device.latitude);
                        const newLng = parseFloat(e.device.longitude);
                        currentLat = newLat; currentLng = newLng;
                        updateWeather(newLat, newLng);
                        if(typeof marker !== 'undefined') marker.setLatLng([newLat, newLng]);
                        if(typeof map !== 'undefined') map.panTo([newLat, newLng]);
                    }
                });
        }

        // --- Chart.js Setup ---
        const depthChartCtx = document.getElementById('depthChart');
        let depthChart;
        if(depthChartCtx) {
            const ctx = depthChartCtx.getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');   
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
            
            depthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Depth (cm)',
                        data: chartData,
                        borderColor: '#3b82f6',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointRadius: 0,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: false },
                        y: {
                            beginAtZero: false,
                            min: 12.0,
                            max: 14.0,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 10, family: 'Rajdhani' } }
                        }
                    }
                }
            });
        }

        // --- Sparkline Setup ---
        const sparklineCtx = document.getElementById('tma-sparkline');
        let sparklineChart;
        if(sparklineCtx) {
            const ctx2 = sparklineCtx.getContext('2d');
            let grad = ctx2.createLinearGradient(0, 0, 0, 80);
            grad.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
            grad.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            sparklineChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        borderColor: '#60a5fa',
                        backgroundColor: grad,
                        borderWidth: 2,
                        pointRadius: 0,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        x: { display: false },
                        y: { display: false, min: 12.0, max: 14.0 }
                    },
                    animation: { duration: 0 }
                }
            });
        }

        // --- Sentinel GIS Map Setup ---
        let map, marker;
        const mapContainer = document.getElementById('sentinel-map');
        if (mapContainer) {
            map = L.map('sentinel-map', {
                center: [currentLat, currentLng],
                zoom: 14,
                zoomControl: false,
                attributionControl: false
            });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19
            }).addTo(map);
            
            // Fix Leaflet rendering bug inside animated/hidden containers
            setTimeout(() => {
                map.invalidateSize();
            }, 1500);

            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div class="relative">
                        <div id="marker-pulse" class="sentinel-pulse"></div>
                       </div>`,
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            marker = L.marker([currentLat, currentLng], { icon: customIcon }).addTo(map);
            
            // Auto-resize map on container change
            setTimeout(() => map.invalidateSize(), 500);
        }

        // --- Utilities & Services ---
        function triggerMapPulse(distance) {
            const pulseEl = document.getElementById('marker-pulse');
            if(!pulseEl) return;
            const tma = SENSOR_ELEVATION_MDPL - (distance / 100);
            if (tma >= SIAGA_1_TMA) pulseEl.classList.add('danger');
            else pulseEl.classList.remove('danger');
            pulseEl.style.animation = 'none';
            pulseEl.offsetHeight;
            pulseEl.style.animation = null; 
        }

        async function resolveLocation(lat, lng) {
            // Immediately Update Coordinate HUDs so it doesn't look stalled
            const coordsStr = `${lat.toFixed(7)}, ${lng.toFixed(7)}`;
            updateText('#current-coords-text', coordsStr);
            if (document.getElementById('sky-location-detail').innerText.includes('--') || 
                document.getElementById('sky-location-detail').innerText.includes('FETCHING')) {
                updateText('#sky-location-detail', coordsStr);
            }

            try {
                const geoUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`;
                const geoRes = await fetch(geoUrl, { 
                    headers: { 'Accept-Language': 'id-ID,id;q=0.9' } 
                });
                
                if (!geoRes.ok) throw new Error('Network response was not ok');
                const geoData = await geoRes.json();
                
                if(!geoData || !geoData.address) {
                    return { city: 'SENTINEL', state: 'INDONESIA' };
                }

                const addr = geoData.address;
                const road = addr.road || addr.street || '';
                const village = addr.village || addr.suburb || addr.hamlet || addr.neighbourhood || '';
                const district = addr.city_district || addr.district || addr.municipality || '';
                const regency = addr.city || addr.regency || addr.county || '';
                const province = addr.state || '';
                
                // Pure Address logic: Exclude house numbers and numeric road parts
                const fullParts = [road, village, district, regency, province]
                    .filter(p => p !== '' && p !== undefined)
                    .filter(p => !/^\d+$/.test(p.trim()))
                    .filter(p => !/^no\.?\s*\d+$/i.test(p.trim()));

                const fullStr = fullParts.join(', ').toUpperCase();

                if (fullStr) {
                    updateText('#current-address-text', fullStr);
                    updateText('#sky-location-detail', fullStr);
                }

                return { city: (regency || 'SENTINEL').toUpperCase(), state: (province || 'INDONESIA').toUpperCase() };
            } catch (e) {
                console.warn('[SENTINEL-GEO] Satellite resolution delayed. Staying on coordinates.');
                updateText('#current-address-text', 'GPS SIGNAL STABLE');
                return { city: 'SENTINEL', state: 'INDONESIA' };
            }
        }

        async function updateWeather(lat, lng) {
            const WEATHER_KEY = "{{ env('OPENWEATHER_API_KEY') }}";
            
            // 1. Resolve Location (Non-blocking but aware)
            const locationPromise = resolveLocation(lat, lng);

            if(!WEATHER_KEY || WEATHER_KEY === 'OPENWEATHER_API_KEY') {
                console.warn('[WEATHER] Key missing in .env');
                updateText('#sky-desc', 'SATELLITE KEY MISSING');
                return;
            }

            try {
                // Check Cache
                const cacheKey = `sentinel_weather_${lat.toFixed(2)}_${lng.toFixed(2)}`;
                const cached = localStorage.getItem(cacheKey);
                if(cached) {
                    const data = JSON.parse(cached);
                    if(Date.now() - data.ts < 600000) {
                        applyWeatherData(data.weather);
                        await locationPromise; // Ensure address HUD is also updated
                        return;
                    }
                }

                // Wait for location resolution to get city name for logging (as requested for sync)
                const loc = await locationPromise;

                console.log(`[WEATHER] Satellite Link Active for ${loc.city}...`);
                const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lng}&appid=${WEATHER_KEY}&units=metric&lang=id`;
                const res = await fetch(url);
                const apiData = await res.json();

                if(apiData.main) {
                    const weatherObj = {
                        temp: apiData.main.temp,
                        feels: apiData.main.feels_like,
                        humidity: apiData.main.humidity,
                        wind: apiData.wind.speed * 3.6, // m/s to km/h
                        pressure: apiData.main.pressure,
                        desc: apiData.weather[0].description,
                        main: apiData.weather[0].main,
                        ts: Date.now()
                    };
                    localStorage.setItem(cacheKey, JSON.stringify({ts: Date.now(), weather: weatherObj}));
                    applyWeatherData(weatherObj);
                } else if (apiData.cod == 401) {
                    console.warn('[WEATHER] API Key Activation Pending.');
                    updateText('#sky-desc', 'SATELLITE LINK PENDING (401)');
                }
            } catch (e) { 
                console.error('[WEATHER] Satellite comms failed:', e);
                updateText('#sky-desc', 'SIGNAL DISTURBANCE');
            }
        }

        function applyWeatherData(w) {
            const weatherIcons = {
                'Clear': '<i class="fa-solid fa-sun text-amber-400"></i>',
                'Clouds': '<i class="fa-solid fa-cloud-sun text-amber-400"></i>',
                'Rain': '<i class="fa-solid fa-cloud-showers-heavy text-blue-400"></i>',
                'Drizzle': '<i class="fa-solid fa-cloud-rain text-cyan-400"></i>',
                'Thunderstorm': '<i class="fa-solid fa-cloud-bolt text-indigo-400"></i>',
                'Snow': '<i class="fa-solid fa-snowflake text-slate-100"></i>',
                'Atmosphere': '<i class="fa-solid fa-smog text-slate-300"></i>'
            };

            const iconHtml = weatherIcons[w.main] || '<i class="fa-solid fa-cloud text-slate-400"></i>';
            const glowColors = {
                'Clear': 'bg-amber-400/20', 'Clouds': 'bg-blue-400/10', 'Rain': 'bg-indigo-600/20', 
                'Thunderstorm': 'bg-purple-600/20', 'Drizzle': 'bg-cyan-600/20'
            };

            updateHtml('#sky-icon-main', iconHtml);
            updateText('#sky-temp', `${w.temp.toFixed(1)}°C`);
            updateText('#sky-feels', `${Math.round(w.feels)}°`);
            updateText('#sky-desc', w.desc.toUpperCase());
            updateHtml('#sky-wind', `${w.wind.toFixed(1)} <span class="text-[10px] text-slate-400">KM/H</span>`);
            updateHtml('#sky-humidity', `${w.humidity.toFixed(0)} <span class="text-[10px] text-slate-400">%</span>`);
            updateHtml('#sky-pressure', `${w.pressure.toFixed(0)} <span class="text-[10px] text-slate-400">HPA</span>`);

            // Background dynamic glow
            document.querySelectorAll('#weather-bg-glow').forEach(el => {
                el.className = `absolute top-0 right-0 w-96 h-96 rounded-full blur-[100px] -mr-48 -mt-48 transition-all duration-1000 ${glowColors[w.main] || 'bg-slate-400/10'}`;
            });

            // Rainfall detection for correlation (Using main.Rain is unreliable in basic API, so we use 'Rain' group)
            lastRainIntensity = (w.main === 'Rain' || w.main === 'Drizzle') ? 10 : 0; 
        }

        function updateDashboard(distance, validCount) {
            const distFloat = parseFloat(distance);
            const tma_mdpl = SENSOR_ELEVATION_MDPL - (distFloat / 100);
            
            // DOM Selectors for this cycle
            const _logEl = document.getElementById('terminal-log');
            const _signalBar = document.getElementById('signal-bar');
            const _riverWater = document.getElementById('river-water');
            const _statusCard = document.getElementById('status-card');
            const _alertMsg = document.getElementById('alert-message');
            const _alertIcon = document.getElementById('alert-icon');
            const _surfaceBadge = document.getElementById('surface-badge');
            const _surfaceText = document.getElementById('surface-text');
            const _surfaceIcon = document.getElementById('surface-icon');
            const _lastUpdated = document.getElementById('last-updated');
            const _alertBanner = document.getElementById('alert-banner');
            const _mockVoltage = document.getElementById('mock-voltage');
            const _mockPing = document.getElementById('mock-ping');
            const _mockTemp = document.getElementById('mock-temp');

            // Terminal Log
            if(_logEl) {
                const now = new Date().toLocaleTimeString('en-US', {hour12:false});
                const div = document.createElement('div');
                div.className = tma_mdpl >= SIAGA_1_TMA ? 'text-red-400' : (tma_mdpl >= SIAGA_2_TMA ? 'text-orange-400' : 'text-slate-400');
                div.innerHTML = `>> [${now}] TELEMETRY: D ${distFloat.toFixed(1)} | Q ${validCount} | TMA ${tma_mdpl.toFixed(2)}`;
                _logEl.appendChild(div);
                if(_logEl.children.length > 25) _logEl.removeChild(_logEl.firstChild);
                _logEl.scrollTop = _logEl.scrollHeight;
                
                // Update Last Updated Timestamp
                if(_lastUpdated) _lastUpdated.textContent = now;
            }

            // UI Data Sync
            const distToGround = 100.0 - distFloat; // Logic 1 Meter
            updateText('#distance-to-ground', distToGround.toFixed(1));
            updateText('#current-distance', distFloat.toFixed(1));
            
            const distEl = document.getElementById('distance-to-ground');
            if(distEl) {
                if(distToGround >= 0) {
                    distEl.classList.remove('text-slate-400');
                    distEl.classList.add('text-red-500');
                } else {
                    distEl.classList.remove('text-red-500');
                    distEl.classList.add('text-slate-400');
                }
            }

            updateText('#valid-count-display', `${validCount}/20`);
            updateText('#water-level', tma_mdpl.toFixed(2));
            
            // Calculate visual percentage (12.00 mdpl = 0%, 13.00 mdpl = 100% - Skala Simulasi 1 Meter)
            const visualPercent = Math.max(0, Math.min(100, ((tma_mdpl - 12.0) / 1.0) * 100));
            updateText('#water-percent', `${visualPercent.toFixed(0)}%`);

            if(_signalBar) _signalBar.style.width = `${(validCount/20)*100}%`;

            // River Animation HUD
            if(_riverWater) {
                const visualFill = Math.max(8, visualPercent);
                _riverWater.style.height = `${visualFill}%`;
            }

            // Sentinel Health Sync
            if(_mockVoltage) _mockVoltage.textContent = (5.0 + (Math.random() * 0.04 - 0.02)).toFixed(2) + 'V';
            if(_mockPing) _mockPing.textContent = (5 + Math.random() * 10).toFixed(0) + 'ms';
            if(_mockTemp) _mockTemp.textContent = (38 + Math.random() * 5).toFixed(1) + '°C';

            // Alert Logic & Voice
            let statusColor = "slate-100";
            let statusLabel = "NORMAL: SUNGAI CITARUM AMAN";
            let surfaceLabel = "NORMAL";
            let iconClass = "fa-solid fa-circle-check text-emerald-500";
            let surfaceIconClass = "fa-solid fa-circle-check";

            if(tma_mdpl >= SIAGA_1_TMA) {
                statusColor = "red-500";
                statusLabel = "SIAGA 1: BAHAYA BANJIR KRITIS CITARUM!";
                surfaceLabel = "SIAGA 1";
                iconClass = "fa-solid fa-skull-crossbones animate-pulse text-red-500";
                surfaceIconClass = "fa-solid fa-skull-crossbones";
                speakAlert("Peringatan Siaga 1! Tinggi Muka Air Sungai Citarum di Karawang mencapai level awas banjir kritis.");
            } else if(tma_mdpl >= SIAGA_2_TMA) {
                statusColor = "orange-500";
                statusLabel = "SIAGA 2: WASPADA DEBIT CITARUM MENINGKAT!";
                surfaceLabel = "SIAGA 2";
                iconClass = "fa-solid fa-bell animate-pulse text-orange-500";
                surfaceIconClass = "fa-solid fa-bell";
                speakAlert("Peringatan Siaga 2. Debit Sungai Citarum meningkat, warga bantaran harap waspada.");
            } else if(tma_mdpl >= SIAGA_3_TMA) {
                statusColor = "amber-500";
                statusLabel = "SIAGA 3: MONITOR KETINGGIAN AIR CITARUM";
                surfaceLabel = "SIAGA 3";
                iconClass = "fa-solid fa-triangle-exclamation text-amber-500";
                surfaceIconClass = "fa-solid fa-triangle-exclamation";
            }

            if(_statusCard) {
                _statusCard.style.borderColor = `var(--tw-border-opacity, 1) rgb(${tma_mdpl >= SIAGA_3_TMA ? (tma_mdpl >= SIAGA_1_TMA ? '239 68 68' : (tma_mdpl >= SIAGA_2_TMA ? '249 115 22' : '245 158 11')) : '241 245 249'})`;
                _statusCard.className = `glass-panel rounded-3xl p-8 border-2 transition-all duration-500 ${tma_mdpl >= SIAGA_3_TMA ? (tma_mdpl >= SIAGA_1_TMA ? 'shadow-2xl shadow-red-500/10' : (tma_mdpl >= SIAGA_2_TMA ? 'shadow-2xl shadow-orange-500/10' : 'shadow-2xl shadow-amber-500/10')) : 'border-slate-100'}`;
            }

            if(_alertMsg) _alertMsg.textContent = statusLabel;
            if(_alertIcon) _alertIcon.className = iconClass;
            
            if(_alertBanner) {
                _alertBanner.classList.remove('hidden');
                _alertBanner.className = `px-4 py-2 rounded-xl flex items-center space-x-2 transition-all duration-300 ${tma_mdpl >= SIAGA_1_TMA ? 'bg-red-50 text-red-600 border-red-100' : (tma_mdpl >= SIAGA_2_TMA ? 'bg-orange-50 text-orange-600 border-orange-100' : (tma_mdpl >= SIAGA_3_TMA ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'))}`;
            }
            
            if(_surfaceBadge) {
                // Update Surface Badge Style
                _surfaceBadge.className = `surface-badge ${tma_mdpl >= SIAGA_1_TMA ? 'bg-red-600' : (tma_mdpl >= SIAGA_2_TMA ? 'bg-orange-600' : (tma_mdpl >= SIAGA_3_TMA ? 'bg-amber-600' : 'bg-slate-900'))} text-white shadow-xl`;
                if(_surfaceText) _surfaceText.textContent = surfaceLabel;
                if(_surfaceIcon) _surfaceIcon.className = surfaceIconClass;
            }

            // Active Zone Highlighting
            document.querySelectorAll('.level-label').forEach(el => {
                el.classList.remove('active', 'font-black');
                el.classList.add('opacity-30');
            });
            const activeId = tma_mdpl >= SIAGA_1_TMA ? 'label-siaga1' : (tma_mdpl >= SIAGA_2_TMA ? 'label-siaga2' : (tma_mdpl >= SIAGA_3_TMA ? 'label-siaga3' : 'label-normal'));
            const targetLabel = document.getElementById(activeId);
            if(targetLabel) { targetLabel.classList.add('active', 'font-black'); targetLabel.classList.remove('opacity-30'); }

            // Peak/Base Session Records
            if (tma_mdpl > maxPeak || maxPeak === 0) { maxPeak = tma_mdpl; updateText('#max-peak-level', maxPeak.toFixed(2)); }
            if (tma_mdpl < minBase || minBase === 9999) { minBase = tma_mdpl; updateText('#min-base-level', minBase.toFixed(2)); }

            // Velocity & ETA Prediction
            if (previousDepth > 0) {
                const delta = tma_mdpl - previousDepth;
                updateText('#flow-velocity', Math.abs(delta).toFixed(2));
                const etaText = predictETA(tma_mdpl) || 'STABLE';
                updateText('#eta-overflow', etaText);
                
                const etaCard = document.getElementById('eta-card');
                const etaIcon = document.getElementById('eta-icon');
                const etaWrapper = document.getElementById('eta-icon-wrapper');
                const etaLabel = document.getElementById('eta-label');
                if(etaCard) {
                    if(etaText === 'STABLE' || etaText.includes('>1 Jam')) {
                        etaCard.className = 'col-span-2 rounded-3xl p-5 bg-white border border-slate-100 shadow-xl flex items-center justify-between relative overflow-hidden group';
                        if(etaWrapper) etaWrapper.className = 'h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100 relative z-10';
                        if(etaIcon) etaIcon.className = 'fa-solid fa-shield-check text-blue-500';
                        if(etaLabel) etaLabel.className = 'text-[9px] text-blue-500 font-black mb-1 uppercase tracking-[0.2em]';
                    } else if(etaText === 'IMMINENT') {
                        etaCard.className = 'col-span-2 rounded-3xl p-5 bg-red-50 border border-red-100 shadow-lg shadow-red-500/20 flex items-center justify-between relative overflow-hidden group animate-pulse';
                        if(etaWrapper) etaWrapper.className = 'h-10 w-10 rounded-full bg-red-100 flex items-center justify-center border border-red-200 relative z-10';
                        if(etaIcon) etaIcon.className = 'fa-solid fa-skull-crossbones text-red-500 animate-bounce';
                        if(etaLabel) etaLabel.className = 'text-[9px] text-red-500 font-black mb-1 uppercase tracking-[0.2em]';
                    } else {
                        etaCard.className = 'col-span-2 rounded-3xl p-5 bg-orange-50 border border-orange-100 shadow-lg shadow-orange-500/20 flex items-center justify-between relative overflow-hidden group';
                        if(etaWrapper) etaWrapper.className = 'h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center border border-orange-200 relative z-10';
                        if(etaIcon) etaIcon.className = 'fa-solid fa-stopwatch text-orange-500';
                        if(etaLabel) etaLabel.className = 'text-[9px] text-orange-500 font-black mb-1 uppercase tracking-[0.2em]';
                    }
                }
                
                const vCont = document.getElementById('velocity-container');
                const vIcon = document.getElementById('velocity-icon');
                if(vCont && vIcon) {
                    if(delta > 0) {
                        vCont.className = 'flex items-baseline text-red-500 transition-colors';
                        vIcon.className = 'fa-solid fa-arrow-up text-xs mr-1 animate-bounce';
                    } else if(delta < 0) {
                        vCont.className = 'flex items-baseline text-emerald-500 transition-colors';
                        vIcon.className = 'fa-solid fa-arrow-down text-xs mr-1';
                    } else {
                        vCont.className = 'flex items-baseline text-blue-500 transition-colors';
                        vIcon.className = 'fa-solid fa-arrow-right text-xs mr-1';
                    }
                }
            }
            previousDepth = tma_mdpl;

            // Update Graphical HUD
            if(depthChart || sparklineChart) {
                const t = new Date().toLocaleTimeString('en-US', {hour12:false});
                chartData.push(tma_mdpl); chartLabels.push(t);
                if (chartData.length > MAX_SAMPLES) { chartData.shift(); chartLabels.shift(); }
                if(depthChart) depthChart.update();
                if(sparklineChart) sparklineChart.update();
            }

            // Trend Matrix Logic (Hourly snapshots)
            if(Date.now() - lastTrendUpdate > 3600000 || trendData24h.length === 0) {
                trendData24h.push({t: Date.now(), d: tma_mdpl});
                if(trendData24h.length > 24) trendData24h.shift();
                lastTrendUpdate = Date.now();
                updateTrendUI();
            }

            // Correlation HUD
            if (lastRainIntensity > 8 && tma_mdpl >= SIAGA_3_TMA) {
                document.querySelectorAll('#weather-correlation-alert').forEach(el => el.classList.remove('hidden'));
            } else {
                document.querySelectorAll('#weather-correlation-alert').forEach(el => el.classList.add('hidden'));
            }
        }

        // --- Core Functions ---
        function predictETA(newDepth) {
            depthBuffer.push({ t: Date.now(), d: newDepth });
            if (depthBuffer.length > 15) depthBuffer.shift();
            if (depthBuffer.length < 5) return null;
            let xS=0, yS=0, xxS=0, xyS=0, n=depthBuffer.length, t0=depthBuffer[0].t;
            depthBuffer.forEach(p => {
                let x=(p.t-t0)/1000, y=p.d;
                xS+=x; yS+=y; xxS+=x*x; xyS+=x*y;
            });
            let m=(n*xyS - xS*yS)/(n*xxS - xS*xS); // m is slope (mdpl per second)
            if (m <= 0.005) return 'STABLE'; // naik terlalu pelan
            let rem = SIAGA_1_TMA - newDepth;
            if (rem <= 0) return 'IMMINENT';
            let sec = Math.round(rem/m);
            if (sec < 0) return 'STABLE';
            return sec > 3600 ? '>1 Jam' : `${Math.floor(sec/60)}m ${sec%60}s`;
        }

        function speakAlert(text) {
            if (Date.now() - lastVoiceTime < 30000) return;
            if ('speechSynthesis' in window) {
                const u = new SpeechSynthesisUtterance(text);
                u.lang='id-ID'; u.rate=0.9;
                window.speechSynthesis.speak(u);
                lastVoiceTime = Date.now();
            }
        }

        function updateTrendUI() {
            const el = document.getElementById('trend-heatmap');
            if(!el) return;
            el.innerHTML = '';
            trendData24h.forEach(i => {
                const b = document.createElement('div');
                b.className = 'flex-1 rounded-full';
                const percent = Math.max(0, Math.min(100, ((i.d - 12.0) / 1.0) * 100));
                b.style.height = `${Math.max(10, percent)}%`;
                b.style.backgroundColor = i.d >= SIAGA_2_TMA ? '#f97316' : '#3b82f6';
                el.appendChild(b);
            });
        }

        // --- Initial Load ---
        updateWeather(currentLat, currentLng);
        updateTrendUI();
        setInterval(() => updateWeather(currentLat, currentLng), 600000);
    });
</script>
