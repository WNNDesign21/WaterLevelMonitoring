<!-- SENTINEL SYSTEM UTILITIES -->
<script>
    function predictETA(newDepth) {
        depthBuffer.push({ t: Date.now(), d: newDepth });
        if (depthBuffer.length > 15) depthBuffer.shift();
        if (depthBuffer.length < 5) return null;
        let xS=0, yS=0, xxS=0, xyS=0, n=depthBuffer.length, t0=depthBuffer[0].t;
        depthBuffer.forEach(p => {
            let x=(p.t-t0)/1000, y=p.d;
            xS+=x; yS+=y; xxS+=x*x; xyS+=x*y;
        });
        let m=(n*xyS - xS*yS)/(n*xxS - xS*xS);
        if (m <= 0.005) return 'STABLE';
        let rem = SIAGA_1_TMA - newDepth;
        if (rem <= 0) return 'IMMINENT';
        let sec = Math.round(rem/m);
        if (sec < 0) return 'STABLE';
        return sec > 3600 ? '>1 Jam' : `${Math.floor(sec/60)}m ${sec%60}s`;
    }

    function updateETACard(etaText) {
        const etaCard = document.getElementById('eta-card');
        const etaIcon = document.getElementById('eta-icon');
        const etaWrapper = document.getElementById('eta-icon-wrapper');
        const etaLabel = document.getElementById('eta-label');
        if(!etaCard) return;
        
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

    function speakAlert(text) {
        if (Date.now() - lastVoiceTime < 30000) return;
        if ('speechSynthesis' in window) {
            const u = new SpeechSynthesisUtterance(text);
            const voices = window.speechSynthesis.getVoices();
            const idVoice = voices.find(voice => voice.lang.includes('id-ID') || voice.lang.includes('id_ID'));
            if (idVoice) u.voice = idVoice;
            u.lang = 'id-ID'; u.rate = 0.9; u.pitch = 1.0;
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

    function updateCalibrationVisualizer() {
        const gapVal = parseFloat(document.getElementById('input_sensor_to_bank')?.value) || 0;
        const tankVal = parseFloat(document.getElementById('input_river_depth')?.value) || 0;
        const elevVal = parseFloat(document.getElementById('input_elevation')?.value) || 0;
        const visGapVal = document.getElementById('vis-gap-val'), visTankVal = document.getElementById('vis-tank-val'), visElevVal = document.getElementById('vis-elevation-val');
        if(visGapVal) visGapVal.textContent = gapVal;
        if(visTankVal) visTankVal.textContent = tankVal;
        if(visElevVal) visElevVal.textContent = elevVal.toFixed(2);
        const total = gapVal + tankVal;
        let gapHeight = 50, tankHeight = 100;
        if (total > 0) {
            const minPx = 40, availablePx = 250 - (minPx * 2);
            gapHeight = minPx + (gapVal / total) * availablePx;
            tankHeight = minPx + (tankVal / total) * availablePx;
        }
        const gapContainer = document.getElementById('vis-gap-container'), tankContainer = document.getElementById('vis-tank-container');
        if(gapContainer) gapContainer.style.height = `${gapHeight}px`;
        if(tankContainer) tankContainer.style.height = `${tankHeight}px`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['input_sensor_to_bank', 'input_river_depth', 'input_elevation'].forEach(id => {
            const el = document.getElementById(id);
            if(el) el.addEventListener('input', updateCalibrationVisualizer);
        });
    });
</script>
