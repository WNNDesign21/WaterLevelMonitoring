<script>
    let historyChart;
    let currentHistoryRange = 'daily';

    function initHistoryChart() {
        const ctx = document.getElementById('historyMainChart')?.getContext('2d');
        if (!ctx) return;

        const isDark = document.getElementById('historyMainChart').closest('.bg-slate-900') !== null;

        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        if (isDark) {
            gradient.addColorStop(0, 'rgba(6, 182, 212, 0.4)');
            gradient.addColorStop(1, 'rgba(6, 182, 212, 0.0)');
        } else {
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
        }

        historyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Elevasi Air (MDPL)',
                    data: [],
                    borderColor: isDark ? '#06b6d4' : '#3b82f6',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 6,
                    pointBackgroundColor: isDark ? '#06b6d4' : '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                        titleColor: isDark ? '#cbd5e1' : '#1e293b',
                        bodyColor: isDark ? '#fff' : '#1e293b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                return ` Elevasi: ${context.parsed.y.toFixed(2)} MDPL`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: isDark ? '#64748b' : '#94a3b8',
                            font: { size: 10, family: 'Rajdhani' },
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 12
                        }
                    },
                    y: {
                        grid: { 
                            color: isDark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.5)',
                            drawBorder: false
                        },
                        ticks: {
                            color: isDark ? '#64748b' : '#94a3b8',
                            font: { size: 10, family: 'Rajdhani' },
                            callback: function(value) { return value.toFixed(2); }
                        }
                    }
                }
            }
        });
    }

    async function fetchHistoryData(range, start = null, end = null) {
        const loading = document.getElementById('history-chart-loading');
        if (loading) loading.classList.remove('hidden');

        try {
            const slug = window.currentDeviceSlug || '{{ $primaryDevice->slug ?? "" }}';
            let url = `/api/water-level/history?device_slug=${slug}&range=${range}`;
            
            if (range === 'custom' && start && end) {
                url += `&start_date=${start}&end_date=${end}`;
            }

            const response = await fetch(url);
            const res = await response.json();

            if (res.status === 'success') {
                updateHistoryUI(res.data);
                
                // Update Badge Name in History
                const nameEl = document.getElementById('history-device-name');
                if (nameEl) nameEl.textContent = res.device;
            }
        } catch (error) {
            console.error('Failed to fetch history:', error);
        } finally {
            if (loading) loading.classList.add('hidden');
        }
    }

    function updateHistoryUI(data) {
        if (!historyChart) return;

        const labels = data.map(item => {
            const date = new Date(item.t);
            if (currentHistoryRange === 'daily') {
                return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } else if (currentHistoryRange === 'yearly') {
                return date.toLocaleDateString('id-ID', { month: 'short', year: '2-digit' });
            } else {
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit' });
            }
        });

        const values = data.map(item => item.y);

        historyChart.data.labels = labels;
        historyChart.data.datasets[0].data = values;
        historyChart.update();

        // Update Stats
        if (data.length > 0) {
            const sum = values.reduce((a, b) => a + b, 0);
            const avg = sum / data.length;
            const max = Math.max(...values);
            const min = Math.min(...values);

            document.getElementById('hist-avg-tma').textContent = avg.toFixed(2) + ' m';
            document.getElementById('hist-max-tma').textContent = max.toFixed(2) + ' m';
            document.getElementById('hist-min-tma').textContent = min.toFixed(2) + ' m';
            document.getElementById('hist-sample-count').textContent = data.length;
        } else {
            ['hist-avg-tma', 'hist-max-tma', 'hist-min-tma', 'hist-sample-count'].forEach(id => {
                document.getElementById(id).textContent = '--';
            });
        }
    }

    window.updateHistoryRange = function(range, btn) {
        currentHistoryRange = range;
        
        // UI Button Switch
        document.querySelectorAll('.history-range-btn').forEach(b => {
            b.classList.remove('bg-white', 'text-blue-600', 'text-cyan-600', 'shadow-sm', 'border', 'border-blue-100', 'border-cyan-100');
            b.classList.add('text-slate-500');
        });

        const isDark = btn.closest('.bg-slate-900') !== null;
        
        btn.classList.add('bg-white', 'shadow-sm', 'border');
        if (isDark) {
            btn.classList.add('text-cyan-600', 'border-cyan-100');
        } else {
            btn.classList.add('text-blue-600', 'border-blue-100');
        }
        btn.classList.remove('text-slate-500');

        if (range === 'custom') {
            const start = document.getElementById('history-start-date').value;
            const end = document.getElementById('history-end-date').value;
            if (!start || !end) {
                alert('Pilih rentang tanggal terlebih dahulu!');
                return;
            }
            fetchHistoryData('custom', start, end);
        } else {
            fetchHistoryData(range);
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        initHistoryChart();
        fetchHistoryData('daily');

        // Watch for device switches to reload history
        const originalSwitchDevice = window.switchDevice;
        if (typeof originalSwitchDevice === 'function') {
            window.switchDevice = function(slug, name, lat, lng) {
                originalSwitchDevice(slug, name, lat, lng);
                fetchHistoryData(currentHistoryRange);
            };
        }
    });
</script>
