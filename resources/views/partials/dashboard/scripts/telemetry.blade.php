<!-- SENTINEL TELEMETRY CORE -->
<script>
    function updateDashboard(response) {
        const data = response.data;
        const config = response.config || { elevation_mdpl: 14.0, sensor_to_bank: 100, river_depth: 100 };
        
        const serverTime = Date.now(); 
        const lastDataTime = new Date(data.created_at).getTime();
        const diffSeconds = (serverTime - lastDataTime) / 1000;
        const isStale = diffSeconds > 30;

        if (isStale) {
            updateText('#water-level', '--');
            updateText('#water-percent', 'OFF');
            const _waterVisual = document.getElementById('river-water');
            if(_waterVisual) _waterVisual.style.height = '0%';
            updateText('#current-distance', '--');
            updateText('#distance-to-ground', '--');
            updateText('#flow-velocity', '0.00');
            updateText('#connection-text', 'SATELLITE_OFFLINE');
            updateText('#it-voltage', '0.00 V');
            updateText('#it-connections', '0');
            updateText('#it-memory', '-- / --');
            updateText('#it-cpu-temp', '--');
            updateText('#it-ping', '0');
            updateText('#it-qps', '0');
            updateText('#it-uptime', '0%');
            const cpuBar = document.getElementById('it-cpu-bar');
            if(cpuBar) cpuBar.style.width = '0%';
            const dot = document.getElementById('connection-dot');
            if(dot) dot.className = 'w-2 h-2 rounded-full bg-red-500';
            return;
        }

        updateText('#connection-text', 'SENTINEL ONLINE');
        const dot = document.getElementById('connection-dot');
        if(dot) dot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse';

        const distFloat = parseFloat(data.distance);
        const validCount = data.valid_count || 0;
        const tma_mdpl = SENSOR_ELEVATION_MDPL - (distFloat / 100);
        
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

        if(_logEl) {
            const now = new Date().toLocaleTimeString('en-US', {hour12:false});
            const div = document.createElement('div');
            div.className = tma_mdpl >= SIAGA_1_TMA ? 'text-red-400' : (tma_mdpl >= SIAGA_2_TMA ? 'text-orange-400' : 'text-slate-400');
            div.innerHTML = `>> [${now}] TELEMETRY: D ${distFloat.toFixed(1)} | Q ${validCount} | TMA ${tma_mdpl.toFixed(2)}`;
            _logEl.appendChild(div);
            if(_logEl.children.length > 25) _logEl.removeChild(_logEl.firstChild);
            _logEl.scrollTop = _logEl.scrollHeight;
            if(_lastUpdated) _lastUpdated.textContent = now;
        }

        const elevation_mdpl = parseFloat(config.elevation_mdpl);
        const sensor_to_bank = parseInt(config.sensor_to_bank);
        const river_depth = parseInt(config.river_depth);

        const distToGround = sensor_to_bank - distFloat;
        updateText('#distance-to-ground', distToGround.toFixed(1));
        updateText('#current-distance', distFloat.toFixed(1));
        
        const distEl = document.getElementById('distance-to-ground');
        if(distEl) {
            if(distToGround >= 0) { distEl.classList.remove('text-slate-400'); distEl.classList.add('text-red-500'); }
            else { distEl.classList.remove('text-red-500'); distEl.classList.add('text-slate-400'); }
        }

        updateText('#valid-count-display', `${validCount}/20`);
        const tma_mdpl_dynamic = elevation_mdpl - (distFloat / 100.0);
        updateText('#water-level', tma_mdpl_dynamic.toFixed(2));
        
        const maxMdpl = elevation_mdpl - (sensor_to_bank / 100.0);
        const minMdpl = maxMdpl - (river_depth / 100.0);
        const visualPercent = Math.max(0, Math.min(100, ((tma_mdpl_dynamic - minMdpl) / (maxMdpl - minMdpl)) * 100));
        updateText('#water-percent', `${visualPercent.toFixed(0)}%`);

        if(_signalBar) _signalBar.style.width = `${(validCount/20)*100}%`;
        if(_riverWater) _riverWater.style.height = `${Math.max(8, visualPercent)}%`;

        if(_mockVoltage) _mockVoltage.textContent = (5.0 + (Math.random() * 0.04 - 0.02)).toFixed(2) + 'V';
        if(_mockPing) _mockPing.textContent = (5 + Math.random() * 10).toFixed(0) + 'ms';
        if(_mockTemp) _mockTemp.textContent = (38 + Math.random() * 5).toFixed(1) + '°C';

        let statusColor = "slate-100", statusLabel = "NORMAL: SUNGAI CITARUM AMAN", surfaceLabel = "NORMAL", iconClass = "fa-solid fa-circle-check text-emerald-500", surfaceIconClass = "fa-solid fa-circle-check";

        if(tma_mdpl >= SIAGA_1_TMA) {
            statusColor = "red-500"; statusLabel = "SIAGA 1: BAHAYA BANJIR KRITIS CITARUM!"; surfaceLabel = "SIAGA 1"; iconClass = "fa-solid fa-skull-crossbones animate-pulse text-red-500"; surfaceIconClass = "fa-solid fa-skull-crossbones";
            speakAlert("Peringatan Siaga 1! Tinggi Muka Air Sungai Citarum di Karawang mencapai level awas banjir kritis.");
        } else if(tma_mdpl >= SIAGA_2_TMA) {
            statusColor = "orange-500"; statusLabel = "SIAGA 2: WASPADA DEBIT CITARUM MENINGKAT!"; surfaceLabel = "SIAGA 2"; iconClass = "fa-solid fa-bell animate-pulse text-orange-500"; surfaceIconClass = "fa-solid fa-bell";
            speakAlert("Peringatan Siaga 2. Debit Sungai Citarum meningkat, warga bantaran harap waspada.");
        } else if(tma_mdpl >= SIAGA_3_TMA) {
            statusColor = "amber-500"; statusLabel = "SIAGA 3: MONITOR KETINGGIAN AIR CITARUM"; surfaceLabel = "SIAGA 3"; iconClass = "fa-solid fa-triangle-exclamation text-amber-500"; surfaceIconClass = "fa-solid fa-triangle-exclamation";
        }

        if(_statusCard) {
            _statusCard.style.borderColor = `var(--tw-border-opacity, 1) rgb(${tma_mdpl >= SIAGA_3_TMA ? (tma_mdpl >= SIAGA_1_TMA ? '239 68 68' : (tma_mdpl >= SIAGA_2_TMA ? '249 115 22' : '245 158 11')) : '241 245 249'})`;
            _statusCard.className = `glass-panel rounded-3xl p-8 border-2 transition-all duration-500 ${tma_mdpl >= SIAGA_3_TMA ? (tma_mdpl >= SIAGA_1_TMA ? 'shadow-2xl shadow-red-500/10' : (tma_mdpl >= SIAGA_2_TMA ? 'shadow-2xl shadow-orange-500/10' : 'shadow-2xl shadow-amber-500/10')) : 'border-slate-100'}`;
        }

        if(_alertMsg) _alertMsg.textContent = statusLabel;
        if(_alertIcon) _alertIcon.className = iconClass;
        if(_alertBanner) _alertBanner.className = `px-4 py-2 rounded-xl flex items-center space-x-2 transition-all duration-300 ${tma_mdpl >= SIAGA_1_TMA ? 'bg-red-50 text-red-600 border-red-100' : (tma_mdpl >= SIAGA_2_TMA ? 'bg-orange-50 text-orange-600 border-orange-100' : (tma_mdpl >= SIAGA_3_TMA ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'))}`;
        if(_surfaceBadge) {
            _surfaceBadge.className = `surface-badge ${tma_mdpl >= SIAGA_1_TMA ? 'bg-red-600' : (tma_mdpl >= SIAGA_2_TMA ? 'bg-orange-600' : (tma_mdpl >= SIAGA_3_TMA ? 'bg-amber-600' : 'bg-slate-900'))} text-white shadow-xl`;
            if(_surfaceText) _surfaceText.textContent = surfaceLabel;
            if(_surfaceIcon) _surfaceIcon.className = surfaceIconClass;
        }

        document.querySelectorAll('.level-label').forEach(el => { el.classList.remove('active', 'font-black'); el.classList.add('opacity-30'); });
        const activeId = tma_mdpl >= SIAGA_1_TMA ? 'label-siaga1' : (tma_mdpl >= SIAGA_2_TMA ? 'label-siaga2' : (tma_mdpl >= SIAGA_3_TMA ? 'label-siaga3' : 'label-normal'));
        const targetLabel = document.getElementById(activeId);
        if(targetLabel) { targetLabel.classList.add('active', 'font-black'); targetLabel.classList.remove('opacity-30'); }

        if (tma_mdpl > maxPeak || maxPeak === 0) { maxPeak = tma_mdpl; updateText('#max-peak-level', maxPeak.toFixed(2)); }
        if (tma_mdpl < minBase || minBase === 9999) { minBase = tma_mdpl; updateText('#min-base-level', minBase.toFixed(2)); }

        if (previousDepth > 0) {
            const delta = tma_mdpl - previousDepth;
            updateText('#flow-velocity', Math.abs(delta).toFixed(2));
            const etaText = predictETA(tma_mdpl) || 'STABLE';
            updateText('#eta-overflow', etaText);
            updateETACard(etaText);
        }
        previousDepth = tma_mdpl;

        if(depthChart || sparklineChart) {
            const t = new Date().toLocaleTimeString('en-US', {hour12:false});
            chartData.push(tma_mdpl); chartLabels.push(t);
            if (chartData.length > MAX_SAMPLES) { chartData.shift(); chartLabels.shift(); }
            if(depthChart) depthChart.update();
            if(sparklineChart) sparklineChart.update();
        }

        if(Date.now() - lastTrendUpdate > 3600000 || trendData24h.length === 0) {
            trendData24h.push({t: Date.now(), d: tma_mdpl});
            if(trendData24h.length > 24) trendData24h.shift();
            lastTrendUpdate = Date.now();
            updateTrendUI();
        }

        if (lastRainIntensity > 8 && tma_mdpl >= SIAGA_3_TMA) document.querySelectorAll('#weather-correlation-alert').forEach(el => el.classList.remove('hidden'));
        else document.querySelectorAll('#weather-correlation-alert').forEach(el => el.classList.add('hidden'));
    }

    function pollTelemetry() {
        if(!window.currentDeviceSlug) return;
        fetch('/api/sensor-data/latest/' + window.currentDeviceSlug)
            .then(res => res.json())
            .then(res => { if (res.status === 'success') updateDashboard(res); })
            .catch(err => console.warn('Polling error:', err));
    }

    // --- AUTOMATIC TELEMETRY REFRESH CYCLE (3 SECONDS) ---
    setInterval(pollTelemetry, 3000);
</script>
