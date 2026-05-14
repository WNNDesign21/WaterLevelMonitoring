<!-- SENTINEL WEBSOCKET & DEVICE CONTROL -->
<script>
    window.echoInstance = null;
    window.currentDeviceSlug = '{{ $primaryDevice->slug ?? "cybernova-s400-primary" }}';
    
    if(typeof Echo !== 'undefined') {
        window.echoInstance = new Echo({
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
        window.echoInstance.channel('sensor-data.' + window.currentDeviceSlug)
            .listen('.sensor.updated', (e) => {
                if(e.sensorData) {
                    updateDashboard({ data: e.sensorData, config: e.config });
                }
            });
            
        // Expose switchDevice globally
        window.switchDevice = function(slug, name, lat, lng) {
            console.log(`[SENTINEL-SYSTEM] Initiating Global Switch to: ${slug} (${lat}, ${lng})`);
            if(window.currentDeviceSlug === slug) return;
            
            if(name) {
                const badge = document.getElementById('active-device-name');
                if(badge) { badge.textContent = name; badge.title = name; }
            }

            if(lat && lng) {
                currentLat = parseFloat(lat);
                currentLng = parseFloat(lng);
                
                if(window.sentinelMap) {
                    window.sentinelMap.flyTo([currentLat, currentLng], 14, { duration: 1.5 });
                    if(window.sentinelMarker) window.sentinelMarker.setLatLng([currentLat, currentLng]);
                    setTimeout(() => window.sentinelMap.invalidateSize(), 500);
                }
                
                const cacheKey = `sentinel_weather_${currentLat.toFixed(4)}_${currentLng.toFixed(4)}`;
                localStorage.removeItem(cacheKey);
                window.updateWeather(currentLat, currentLng);
            }
            
            window.echoInstance.leave('sensor-data.' + window.currentDeviceSlug);
            window.currentDeviceSlug = slug;
            
            updateText('#current-distance', '--');
            updateText('#water-level', '--');
            updateText('#flow-velocity', '0.00');
            
            window.echoInstance.channel('sensor-data.' + window.currentDeviceSlug)
                .listen('.sensor.updated', (e) => {
                    if(e.sensorData) {
                        updateDashboard({ data: e.sensorData, config: e.config });
                    }
                });
            
            if (typeof pollTelemetry === 'function') pollTelemetry();
        };
    }
</script>
