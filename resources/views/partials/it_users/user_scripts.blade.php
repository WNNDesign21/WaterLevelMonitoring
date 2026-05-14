<!-- User HQ Specific Logic -->
<script>
    function toggleUserModal() {
        const modal = document.getElementById('userModal'), content = document.getElementById('userModalContent');
        if (modal.classList.contains('invisible')) {
            modal.classList.remove('invisible', 'opacity-0');
            setTimeout(() => content.classList.remove('scale-90', 'opacity-0'), 10);
        } else {
            content.classList.add('scale-90', 'opacity-0');
            setTimeout(() => modal.classList.add('invisible', 'opacity-0'), 300);
        }
    }

    function triggerReveal() {
        if(!document.body.classList.contains('loaded')) {
            document.body.classList.add('loaded');
            if (typeof initAccessTrendChart === 'function') initAccessTrendChart();
        }
    }
    document.addEventListener('DOMContentLoaded', triggerReveal);
    window.addEventListener('load', triggerReveal);
    setTimeout(triggerReveal, 3000);

    function initAccessTrendChart() {
        const chartDom = document.getElementById('accessTrendChart');
        if(!chartDom) return;
        const myChart = echarts.init(chartDom);
        myChart.setOption({
            grid: { top: 10, bottom: 20, left: 10, right: 10 },
            xAxis: { type: 'category', data: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'], axisLine: { show: false }, axisTick: { show: false }, axisLabel: { color: '#94a3b8', fontSize: 8, fontWeight: 'bold' } },
            yAxis: { type: 'value', show: false },
            series: [{ data: [12, 45, 67, 34, 89, 56], type: 'line', smooth: true, showSymbol: false, lineStyle: { width: 3, color: '#3b82f6' }, areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ offset: 0, color: 'rgba(59, 130, 246, 0.2)' }, { offset: 1, color: 'rgba(59, 130, 246, 0)' }]) } }]
        });
        window.addEventListener('resize', () => myChart.resize());
    }

    function toggleSystemLockdown() {
        if(!confirm('MASTER_KILL_SWITCH: Apakah Anda yakin ingin mengubah status akses global?')) return;
        fetch("{{ route('it.system.lockdown.toggle') }}", { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } })
        .then(res => res.json()).then(data => { updateLockdownUI(data.lockdown); alert(data.message); });
    }

    function updateLockdownUI(status) {
        const text = document.getElementById('lockdown-status-text'), bg = document.getElementById('lockdown-switch-bg'), ball = document.getElementById('lockdown-switch-ball'), btn = document.getElementById('lockdown-btn');
        if(!text) return;
        if (status === '1') {
            text.innerText = 'LOCKED'; text.className = 'text-[9px] font-black text-red-500 group-hover:text-white uppercase';
            bg.className = 'w-10 h-6 bg-red-500/20 rounded-full relative p-1 group-hover:bg-white/20 transition-colors';
            ball.className = 'w-4 h-4 bg-red-500 rounded-full group-hover:bg-white transition-all shadow-lg translate-x-4';
            btn.classList.add('border-red-500/50', 'ring-4', 'ring-red-500/10');
        } else {
            text.innerText = 'UNLOCKED'; text.className = 'text-[9px] font-black text-emerald-400 group-hover:text-white uppercase';
            bg.className = 'w-10 h-6 bg-emerald-500/20 rounded-full relative p-1 group-hover:bg-white/20 transition-colors';
            ball.className = 'w-4 h-4 bg-emerald-500 rounded-full group-hover:bg-white transition-all shadow-lg translate-x-0';
            btn.classList.remove('border-red-500/50', 'ring-4', 'ring-red-500/10');
        }
    }

    fetch("{{ route('it.system.status') }}").then(res => res.json()).then(data => updateLockdownUI(data.lockdown));

    function fetchActivityLogs() {
        fetch("{{ route('it.system.logs') }}").then(res => res.json()).then(logs => {
            const container = document.getElementById('activity-log-stream');
            if (!container) return;
            if (logs.length === 0) { container.innerHTML = '<div class="text-center py-10 text-[10px] font-black text-slate-400 uppercase tracking-widest">No activity recorded</div>'; return; }
            container.innerHTML = logs.map(log => `
                <div class="flex space-x-4 group animate-fade-in">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-lg ${getEventBg(log.event)} flex items-center justify-center text-white text-[10px] font-black border shadow-sm transition-all">${log.user.charAt(0)}</div>
                        <div class="w-px h-full bg-slate-100 my-2"></div>
                    </div>
                    <div class="pb-4">
                        <div class="text-[10px] font-black text-slate-800 uppercase tracking-tight">${log.user}</div>
                        <div class="text-[8px] font-bold ${getEventText(log.event)} uppercase tracking-widest mt-1">${log.desc}</div>
                        <div class="flex items-center space-x-3 mt-2">
                            <span class="text-[8px] font-mono text-slate-400"><i class="fa-solid fa-clock mr-1"></i> ${log.time}</span>
                            <span class="text-[8px] font-mono text-blue-500/60 bg-blue-50 px-1.5 py-0.5 rounded-md border border-blue-100/50">${log.ip}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        });
    }

    function getEventBg(event) {
        if (event.includes('failed')) return 'bg-red-500 border-red-600 shadow-red-200';
        if (event.includes('success')) return 'bg-emerald-500 border-emerald-600 shadow-emerald-200';
        if (event.includes('sso')) return 'bg-blue-500 border-blue-600 shadow-blue-200';
        return 'bg-slate-500 border-slate-600 shadow-slate-200';
    }

    function getEventText(event) {
        if (event.includes('failed')) return 'text-red-500';
        if (event.includes('success')) return 'text-emerald-500';
        if (event.includes('sso')) return 'text-blue-500';
        return 'text-slate-500';
    }

    fetchActivityLogs();
    setInterval(fetchActivityLogs, 5000);
</script>
