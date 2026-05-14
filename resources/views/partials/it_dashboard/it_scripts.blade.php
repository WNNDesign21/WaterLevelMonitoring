<!-- IT Specific Dashboard Logic -->
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
            const connectionText = document.getElementById('connection-text')?.textContent;
            if(connectionText && connectionText.includes('OFFLINE')) return;

            const newLog = document.createElement('div');
            newLog.className = 'text-slate-500';
            const randLog = logs[Math.floor(Math.random() * logs.length)];
            const time = new Date().toISOString().split('T')[1].substring(0, 8);
            newLog.textContent = `[${time}] ${randLog}`;
            terminal.appendChild(newLog);
            if (terminal.children.length > 50) terminal.removeChild(terminal.firstChild);
            const terminalContainer = document.getElementById('it-terminal');
            if(terminalContainer) terminalContainer.scrollTop = terminalContainer.scrollHeight;
        }, 2000);

        // --- 2. Micro Metrics Randomizer ---
        setInterval(() => {
            const globalStatus = document.getElementById('global-status-text')?.textContent;
            if(globalStatus && (globalStatus.includes('OFFLINE') || globalStatus.includes('DISCONNECTED'))) {
                ['it-uptime', 'it-ping', 'it-qps', 'it-voltage', 'it-connections', 'it-memory', 'it-cpu-temp', 'it-packet-loss', 'it-signal-strength'].forEach(id => {
                    const el = document.getElementById(id);
                    if(el) el.textContent = id === 'it-uptime' ? '0%' : (id === 'it-voltage' ? '0.00 V' : (id === 'it-memory' ? '-- / --' : '0'));
                });
                ['it-cpu-bar', 'it-packet-bar', 'it-signal-bar'].forEach(id => {
                    const el = document.getElementById(id);
                    if(el) el.style.width = '0%';
                });
                return;
            }
            
            document.getElementById('it-ping').textContent = Math.floor(Math.random() * 5) + 10;
            document.getElementById('it-qps').textContent = Math.floor(Math.random() * 100) + 400;
            document.getElementById('it-voltage').textContent = (5.00 + Math.random() * 0.1).toFixed(2) + ' V';
            document.getElementById('it-connections').textContent = (1200 + Math.floor(Math.random() * 50)).toLocaleString();
            const cpuVal = 45 + Math.floor(Math.random() * 10);
            document.getElementById('it-cpu-temp').textContent = cpuVal + '°C';
            document.getElementById('it-cpu-bar').style.width = cpuVal + '%';
            const signalVal = 70 + Math.floor(Math.random() * 15);
            document.getElementById('it-signal-strength').textContent = '-' + (100 - signalVal) + ' dBm';
            document.getElementById('it-signal-bar').style.width = signalVal + '%';
        }, 1000);

        // --- 3. HEARTBEAT CHECK ---
        function checkDeviceHeartbeat() {
            fetch('/api/devices/heartbeat')
                .then(res => res.json())
                .then(data => {
                    data.devices.forEach(device => {
                        const dot = document.getElementById(`status-dot-${device.slug}`);
                        if (dot) dot.className = `w-2 h-2 rounded-full ${device.is_online ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]' : 'bg-red-500'} shrink-0`;
                        if (document.getElementById('active-device-name')?.textContent === device.name) updateGlobalBadge(device.is_online);
                    });
                });
        }

        function updateGlobalBadge(isOnline) {
            const badge = document.getElementById('global-connectivity-badge'), dot = document.getElementById('global-status-dot'), ping = document.getElementById('global-status-ping'), text = document.getElementById('global-status-text');
            if (isOnline) {
                badge.className = 'flex items-center space-x-3 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl shadow-sm transition-all duration-500';
                dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 block';
                ping.className = 'absolute inset-0 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping opacity-75';
                text.className = 'text-[10px] font-black text-emerald-700 uppercase tracking-widest';
                text.textContent = 'SENTINEL_ONLINE';
            } else {
                badge.className = 'flex items-center space-x-3 bg-red-50 border border-red-100 px-4 py-2 rounded-xl shadow-sm transition-all duration-500';
                dot.className = 'w-2.5 h-2.5 rounded-full bg-red-500 block'; ping.className = 'hidden'; text.className = 'text-[10px] font-black text-red-700 uppercase tracking-widest'; text.textContent = 'SENTINEL_OFFLINE';
            }
        }
        setInterval(checkDeviceHeartbeat, 5000);
        checkDeviceHeartbeat();

        // --- 4. AI ETA Card Observer ---
        const etaOverflow = document.getElementById('eta-overflow'), etaCard = document.getElementById('it-eta-card');
        if(etaOverflow && etaCard) {
            new MutationObserver(() => {
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
            }).observe(etaOverflow, { childList: true, characterData: true, subtree: true });
        }
    });

    function triggerReveal() {
        if(!document.body.classList.contains('loaded')) document.body.classList.add('loaded'); 
    }
    document.addEventListener('DOMContentLoaded', triggerReveal);
    window.addEventListener('load', triggerReveal);
    setTimeout(triggerReveal, 3000);

    function toggleDeviceSelector() {
        const menu = document.getElementById('device-selector-menu'), icon = document.getElementById('device-selector-icon');
        if (menu.classList.contains('invisible')) {
            menu.classList.remove('invisible', 'scale-y-0', 'opacity-0'); menu.classList.add('scale-y-100', 'opacity-100'); icon.classList.add('rotate-180');
        } else {
            menu.classList.remove('scale-y-100', 'opacity-100'); menu.classList.add('scale-y-0', 'opacity-0'); icon.classList.remove('rotate-180');
            setTimeout(() => menu.classList.add('invisible'), 300);
        }
    }

    function userSwitchDevice(slug, name, location, lat, lng) {
        document.getElementById('active-device-name').textContent = name;
        toggleDeviceSelector();
        if(typeof window.switchDevice === 'function') window.switchDevice(slug, name, lat, lng);
    }

    document.addEventListener('click', (e) => {
        if(!document.getElementById('device-selector-wrapper')?.contains(e.target)) {
            const menu = document.getElementById('device-selector-menu');
            if(menu && !menu.classList.contains('invisible')) toggleDeviceSelector();
        }
    });
</script>
