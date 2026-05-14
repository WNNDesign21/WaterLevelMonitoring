<!-- SENTINEL GRAPHICAL ENGINE -->
<script>
    let depthChart;
    let sparklineChart;

    function initCharts() {
        const depthChartCtx = document.getElementById('depthChart');
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

        const sparklineCtx = document.getElementById('tma-sparkline');
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
    }
</script>
