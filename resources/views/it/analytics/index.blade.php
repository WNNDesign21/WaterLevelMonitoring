@include('partials.dashboard.head')

<style>
    @keyframes scanline {
        0% { transform: translateY(-100%); }
        100% { transform: translateY(100%); }
    }
    
    .scanline-effect {
        position: relative;
        overflow: hidden;
    }
    .scanline-effect::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(59, 130, 246, 0.05) 51%);
        background-size: 100% 4px;
        pointer-events: none;
        z-index: 10;
    }

    .cockpit-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .cockpit-card:hover {
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.1);
    }

    .odometer-value {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

<body class="min-h-screen bg-slate-100/50 font-sans antialiased selection:bg-blue-200 p-2">
    
    <div class="flex flex-col h-[calc(100vh-1rem)] gap-2 w-full">
        
        <!-- COMPACT COMMAND HEADER -->
        <header class="cockpit-card rounded-xl p-4 flex items-center justify-between shrink-0 border-blue-100 bg-white/90">
            <div class="flex items-center space-x-8">
                <!-- Branding -->
                <div class="flex items-center space-x-4 border-r border-slate-200 pr-8">
                    <a href="{{ route('it.dashboard') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-800 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm group">
                        <i class="fa-solid fa-chevron-left text-xs group-hover:-translate-x-0.5 transition-transform"></i>
                    </a>
                    <img src="{{ asset('assets/img/logo/WaterSenseIcon.png') }}" class="w-9 h-9 object-contain">
                    <div>
                        <h1 class="text-lg font-black text-slate-800 tracking-tighter uppercase leading-none">Water<span class="text-cyan-600">Sense</span></h1>
                        <p class="text-[8px] font-mono text-slate-400 tracking-[0.4em] uppercase mt-1">ANALYTICS_MISSION_CONTROL</p>
                    </div>
                </div>

                <!-- Global Telemetry -->
                <div class="hidden xl:flex items-center space-x-12">
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">System_Clock</span>
                        <span id="header-time" class="text-sm font-black text-slate-700 font-mono tracking-tighter">00:00:00:000</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Active_Node</span>
                        <span class="text-sm font-black text-blue-600 font-mono" id="metric-node">--</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">System_Latency</span>
                        <span class="text-sm font-black text-emerald-500 font-mono" id="metric-query-time">0.00ms</span>
                    </div>
                </div>
            </div>

            <!-- Navigation & User -->
            <div class="flex items-center space-x-6">
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <a href="{{ route('it.dashboard') }}" class="px-5 py-2 text-[10px] font-black uppercase text-slate-500 hover:text-slate-900 transition-all">Dashboard</a>
                    <a href="{{ route('it.devices.index') }}" class="px-5 py-2 text-[10px] font-black uppercase text-slate-500 hover:text-slate-900 transition-all">Device</a>
                </div>
                <div class="h-10 w-px bg-slate-200 mx-2"></div>
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-black text-slate-800 uppercase leading-none">{{ auth()->user()->name }}</div>
                        <div class="text-[8px] font-bold text-cyan-600 uppercase tracking-widest mt-1.5">IT_COMMANDER</div>
                    </div>
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0f172a&color=fff' }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-md">
                </div>
            </div>
        </header>

        <!-- MAIN MISSION CONTROL GRID -->
        <main class="flex-1 flex gap-2 min-h-0 w-full">
            
            <!-- LEFT CONTROL PANEL (DENSE) -->
            <aside class="w-80 flex flex-col gap-2 shrink-0">
                
                <!-- Filter Section -->
                <section class="cockpit-card rounded-2xl p-5 flex flex-col border-slate-200 bg-white">
                    <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-5 flex items-center">
                        <i class="fa-solid fa-sliders mr-2 text-blue-500"></i> Primary_Inputs
                    </h2>
                    
                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">Infrastructure_Target</label>
                            <select id="device_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-[11px] font-black text-slate-700 focus:ring-2 focus:ring-blue-500/10 outline-none transition-all">
                                @foreach($allDevices as $device)
                                <option value="{{ $device->id }}" {{ $selectedDeviceId == $device->id ? 'selected' : '' }}>{{ $device->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">Time_Horizon</label>
                            <div class="grid grid-cols-3 gap-1.5">
                                <button onclick="setRange('24h')" id="btn-24h" class="range-btn py-2.5 rounded-xl text-[10px] font-black uppercase border border-blue-200 bg-blue-600 text-white shadow-lg shadow-blue-500/20">24H</button>
                                <button onclick="setRange('7d')" id="btn-7d" class="range-btn py-2.5 rounded-xl text-[10px] font-black uppercase border border-slate-200 bg-white text-slate-600">7D</button>
                                <button onclick="setRange('30d')" id="btn-30d" class="range-btn py-2.5 rounded-xl text-[10px] font-black uppercase border border-slate-200 bg-white text-slate-600">30D</button>
                            </div>
                            <input type="hidden" id="range_input" value="24h">
                        </div>

                        <button onclick="loadAnalyticsData()" class="w-full py-3.5 rounded-xl bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-sync-alt mr-2"></i> Update_Horizon
                        </button>
                    </div>
                </section>

                <!-- Statistical Bento -->
                <section class="flex-1 cockpit-card rounded-2xl p-5 flex flex-col border-slate-200 bg-white min-h-0 overflow-hidden">
                    <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-5">Statistical_Matrix</h2>
                    <div class="space-y-4 overflow-y-auto pr-1">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="text-[8px] font-black text-slate-400 uppercase block mb-1.5">Max_Elevation</span>
                            <div class="text-2xl font-black text-slate-800 font-mono tracking-tighter" id="insight-max-tma">0.00 mdpl</div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="text-[8px] font-black text-slate-400 uppercase block mb-1.5">Average_Elevation</span>
                            <div class="text-2xl font-black text-blue-600 font-mono tracking-tighter" id="insight-avg-tma">0.00 mdpl</div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <span class="text-[8px] font-black text-slate-400 uppercase block mb-1.5">Data_Points</span>
                            <div class="text-2xl font-black text-slate-700 font-mono tracking-tighter" id="metric-data-points">0</div>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                            <span class="text-[8px] font-black text-emerald-600 uppercase block mb-1.5">Data_Integrity</span>
                            <div class="text-2xl font-black text-emerald-700 font-mono tracking-tighter">99.98%</div>
                        </div>
                    </div>
                </section>

                <!-- Actions Section -->
                <section class="cockpit-card rounded-2xl p-4 border-slate-200 bg-white shadow-sm">
                    <button onclick="exportData()" class="w-full py-4 rounded-xl bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 shadow-xl shadow-emerald-500/20 transition-all flex items-center justify-center">
                        <i class="fa-solid fa-download mr-2"></i> Export_Dataset_CSV
                    </button>
                </section>
            </aside>

            <!-- CENTER WORKSPACE (EXPANSIVE) -->
            <div class="flex-1 flex flex-col gap-2 min-w-0">
                
                <!-- Main Intelligence View -->
                <section class="flex-1 cockpit-card rounded-[3rem] p-8 border-slate-200 bg-white relative flex flex-col scanline-effect overflow-hidden shadow-2xl">
                    <div class="flex items-center justify-between mb-6 z-20">
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tighter uppercase" id="chart-title">Intelligence_Horizon</h3>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="text-[10px] font-mono font-bold text-blue-500 uppercase tracking-[0.2em] bg-blue-50 px-3 py-1 rounded-md">PREDICTIVE_MODE_ACTIVE</span>
                                <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-[0.2em]" id="chart-subtitle">Linear_Regression_V1.0</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2 px-4 py-2 rounded-xl border border-slate-100 bg-white shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">ACTUAL</span>
                            </div>
                            <div class="flex items-center space-x-2 px-4 py-2 rounded-xl border border-slate-100 bg-white shadow-sm">
                                <span class="w-2 h-2 rounded-full border border-blue-500 border-dashed"></span>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">FORECAST</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div id="main-analytics-chart" class="flex-1 w-full z-20" style="min-height: 0;"></div>

                    <!-- Legend Overlay (Bottom Right) -->
                    <div class="absolute bottom-10 right-10 z-30 pointer-events-none opacity-40 flex flex-col items-end">
                        <span class="text-[8px] font-mono text-slate-400 uppercase tracking-[0.5em]">SENTINEL_SECURE_LINK</span>
                        <span class="text-[8px] font-mono text-slate-400 uppercase tracking-[0.5em] mt-1.5">DECRYPT_KEY_AES256</span>
                    </div>
                </section>

                <!-- Bottom Telemetry Console (New) -->
                <section class="h-40 cockpit-card rounded-2xl p-4 border-slate-200 bg-white overflow-hidden flex flex-col shadow-lg">
                    <div class="flex items-center justify-between mb-3 shrink-0 border-b border-slate-100 pb-2">
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] flex items-center">
                            <span class="w-2 h-2 rounded-full bg-blue-500 mr-3 animate-pulse"></span> System_Event_Log
                        </span>
                        <span class="text-[8px] font-mono text-slate-400 uppercase">Stream_Ready</span>
                    </div>
                    <div id="console-log" class="flex-1 overflow-y-auto font-mono text-[11px] text-slate-600 space-y-1.5 pr-3">
                        <div class="text-emerald-600 font-bold">>> [{{ now()->format('H:i:s') }}] SYSTEM_INITIALIZED_OK</div>
                        <div class="text-blue-600 font-bold">>> [{{ now()->format('H:i:s') }}] ANALYTICS_ENGINE_CONNECTED</div>
                        <div class="text-slate-400">>> [{{ now()->format('H:i:s') }}] LISTENING_FOR_TELEMETRY_STREAM...</div>
                    </div>
                </section>
            </div>

            <!-- RIGHT ANALYTICS SIDEBAR (NEW) -->
            <aside class="w-80 flex flex-col gap-2 shrink-0">
                <!-- Predictive Summary -->
                <section class="cockpit-card rounded-2xl p-5 flex flex-col border-slate-200 bg-white">
                    <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-5">AI_Projection</h2>
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-bold text-slate-400 uppercase">Trend_Vector</span>
                            <span class="text-xs font-black text-emerald-500" id="trend-vector">STABLE</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full w-[65%]"></div>
                        </div>
                        <div class="pt-3">
                            <span class="text-[9px] font-black text-slate-400 uppercase block mb-2.5">Expert_Recommendation</span>
                            <p class="text-xs text-slate-600 leading-relaxed italic">"Ketinggian air dalam batas normal, tren diprediksi stabil dalam 3 interval kedepan."</p>
                        </div>
                    </div>
                </section>

                <!-- Infrastructure Visualization -->
                <section class="flex-1 cockpit-card rounded-2xl p-5 flex flex-col border-slate-200 bg-white relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 opacity-[0.05]">
                        <i class="fa-solid fa-microchip text-[12rem]"></i>
                    </div>
                    <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Architecture_View</h2>
                    <div class="flex-1 flex flex-col justify-center items-center space-y-6">
                        <div class="w-28 h-28 rounded-full border-4 border-dashed border-blue-100 flex items-center justify-center relative">
                            <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center shadow-2xl shadow-blue-500/40">
                                <i class="fa-solid fa-server text-white text-3xl"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 w-7 h-7 bg-emerald-500 border-4 border-white rounded-full flex items-center justify-center shadow-lg">
                                <i class="fa-solid fa-check text-white text-[10px]"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Cloud_Synchronizer</p>
                            <p class="text-[9px] font-bold text-emerald-500 uppercase mt-2 tracking-widest">UPLINK_ENCRYPTED</p>
                        </div>
                    </div>
                </section>
            </aside>
        </main>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script>
        let myChart = null;

        document.addEventListener('DOMContentLoaded', () => {
            initChart();
            loadAnalyticsData();
            
            // Header Time with Milliseconds
            setInterval(() => {
                const now = new Date();
                const ms = now.getMilliseconds().toString().padStart(3, '0');
                const time = now.toLocaleTimeString('id-ID', { hour12: false }).replace(/\./g, ':');
                document.getElementById('header-time').textContent = `${time}:${ms}`;
            }, 50);

            setTimeout(() => document.body.classList.add('loaded'), 300);
        });

        function initChart() {
            const chartDom = document.getElementById('main-analytics-chart');
            myChart = echarts.init(chartDom);
            window.addEventListener('resize', () => myChart.resize());
        }

        function setRange(range) {
            document.getElementById('range_input').value = range;
            document.querySelectorAll('.range-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-500/20', 'border-blue-200');
                btn.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
            });
            const activeBtn = document.getElementById('btn-' + range);
            activeBtn.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
            activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-500/20', 'border-blue-200');
            loadAnalyticsData();
        }

        async function loadAnalyticsData() {
            const deviceId = document.getElementById('device_id').value;
            const range = document.getElementById('range_input').value;

            myChart.showLoading({
                text: 'REFINING_HORIZON_VECTOR...',
                color: '#3b82f6',
                textColor: '#1e293b',
                maskColor: 'rgba(255, 255, 255, 0.8)',
                zlevel: 0
            });

            try {
                const response = await fetch(`{{ route('it.analytics.data') }}?device_id=${deviceId}&range=${range}`);
                const data = await response.json();

                // Update UI elements
                document.getElementById('chart-title').textContent = data.device_name.toUpperCase().replace(/\s+/g, '_');
                document.getElementById('metric-query-time').textContent = data.metrics.query_time + 'ms';
                document.getElementById('metric-node').textContent = data.metrics.processing_node;
                document.getElementById('metric-data-points').textContent = data.metrics.data_points;

                const maxTma = Math.max(...data.tma).toFixed(2);
                const avgTma = (data.tma.reduce((a, b) => a + b, 0) / data.tma.length).toFixed(2);
                document.getElementById('insight-max-tma').textContent = maxTma + ' mdpl';
                document.getElementById('insight-avg-tma').textContent = avgTma + ' mdpl';

                // Log entry
                const log = document.getElementById('console-log');
                const entry = document.createElement('div');
                entry.textContent = `>> [${new Date().toLocaleTimeString('id-ID', {hour12:false})}] FETCH_OK: ${data.metrics.data_points} POINTS IN ${data.metrics.query_time}ms`;
                log.appendChild(entry);
                log.scrollTop = log.scrollHeight;

                // Prepare Chart
                const forecastLabels = [...Array(5).keys()].map(i => 'F+' + (i+1));
                const allLabels = [...data.labels, ...forecastLabels];
                const lastActual = data.tma[data.tma.length - 1];
                const forecastSeries = [...Array(data.tma.length - 1).fill('-'), lastActual, ...data.forecast];

                const option = {
                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        textStyle: { color: '#1e293b', fontSize: 10, fontWeight: 'bold' },
                        axisPointer: { lineStyle: { color: '#3b82f6', type: 'dashed' } }
                    },
                    grid: { left: '3%', right: '4%', bottom: '15%', top: '3%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: allLabels,
                        axisLine: { lineStyle: { color: '#e2e8f0' } },
                        axisLabel: { color: '#94a3b8', fontSize: 8, fontWeight: 'bold', interval: Math.floor(allLabels.length / 10) }
                    },
                    yAxis: {
                        type: 'value',
                        scale: true,
                        splitLine: { lineStyle: { color: '#f1f5f9', type: 'dashed' } },
                        axisLabel: { color: '#94a3b8', fontSize: 8, fontWeight: 'bold' }
                    },
                    dataZoom: [
                        { type: 'inside', start: 0, end: 100 },
                        { type: 'slider', bottom: 10, height: 20, borderColor: 'transparent', backgroundColor: '#f1f5f9', fillerColor: 'rgba(59, 130, 246, 0.1)' }
                    ],
                    series: [
                        {
                            name: 'ACTUAL',
                            type: 'line',
                            smooth: 0.3,
                            symbol: 'none',
                            lineStyle: { width: 3, color: '#3b82f6' },
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                    { offset: 0, color: 'rgba(59, 130, 246, 0.2)' },
                                    { offset: 1, color: 'rgba(59, 130, 246, 0)' }
                                ])
                            },
                            data: data.tma,
                            markLine: {
                                silent: true,
                                data: [
                                    { yAxis: 12.80, lineStyle: { color: '#f59e0b', type: 'dashed', width: 1 }, label: { formatter: 'S2', position: 'end' } },
                                    { yAxis: 13.00, lineStyle: { color: '#ef4444', type: 'solid', width: 1 }, label: { formatter: 'S1', position: 'end' } }
                                ]
                            }
                        },
                        {
                            name: 'FORECAST',
                            type: 'line',
                            smooth: 0.3,
                            symbol: 'circle',
                            symbolSize: 4,
                            lineStyle: { width: 2, color: '#6366f1', type: 'dashed' },
                            data: forecastSeries
                        }
                    ]
                };

                myChart.hideLoading();
                myChart.setOption(option);
            } catch (error) {
                console.error('Failed to load analytics:', error);
                myChart.hideLoading();
            }
        }

        function exportData() {
            const deviceId = document.getElementById('device_id').value;
            const range = document.getElementById('range_input').value;
            window.location.href = `{{ route('it.analytics.export') }}?device_id=${deviceId}&range=${range}`;
        }
    </script>
</body>
</html>
