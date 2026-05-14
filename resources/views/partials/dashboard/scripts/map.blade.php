<!-- SENTINEL GEOSPATIAL INTELLIGENCE -->
<script>
    window.sentinelMap = null;
    window.sentinelMarker = null;

    function initSentinelMap() {
        const mapContainer = document.getElementById('sentinel-map');
        if (!mapContainer) return;

        window.sentinelMap = L.map('sentinel-map', {
            center: [currentLat, currentLng],
            zoom: 14,
            zoomControl: false,
            attributionControl: false
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
        }).addTo(window.sentinelMap);
        
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div class="relative"><div id="marker-pulse" class="sentinel-pulse"></div></div>`,
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        window.sentinelMarker = L.marker([currentLat, currentLng], { icon: customIcon }).addTo(window.sentinelMap);
        setTimeout(() => { if(window.sentinelMap) window.sentinelMap.invalidateSize(); }, 1500);
    }

    async function resolveLocation(lat, lng) {
        const coordsStr = `${lat.toFixed(7)}, ${lng.toFixed(7)}`;
        updateText('#current-coords-text', coordsStr);
        
        try {
            const geoUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`;
            const geoRes = await fetch(geoUrl, { headers: { 'Accept-Language': 'id-ID,id;q=0.9' } });
            const geoData = await geoRes.json();
            
            if(!geoData || !geoData.address) return { city: 'SENTINEL', state: 'INDONESIA' };

            const addr = geoData.address;
            const fullParts = [addr.road || '', addr.village || addr.suburb || '', addr.city_district || addr.district || '', addr.city || addr.regency || '', addr.state || '']
                .filter(p => p !== '' && !/^\d+$/.test(p.trim()));

            const fullStr = fullParts.join(', ').toUpperCase();
            if (fullStr) {
                updateText('#current-address-text', fullStr);
                updateText('#sky-location-detail', fullStr);
            }
            return { city: (addr.city || addr.regency || 'SENTINEL').toUpperCase(), state: (addr.state || 'INDONESIA').toUpperCase() };
        } catch (e) {
            updateText('#current-address-text', 'GPS SIGNAL STABLE');
            return { city: 'SENTINEL', state: 'INDONESIA' };
        }
    }

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
</script>
