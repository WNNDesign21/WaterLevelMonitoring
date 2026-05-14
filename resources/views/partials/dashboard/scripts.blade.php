<!-- SENTINEL CORE SCRIPT ASSEMBLY -->
@include('partials.dashboard.scripts.base_config')
@include('partials.dashboard.scripts.websocket')
@include('partials.dashboard.scripts.charts')
@include('partials.dashboard.scripts.map')
@include('partials.dashboard.scripts.weather')
@include('partials.dashboard.scripts.telemetry')
@include('partials.dashboard.scripts.utilities')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- System Boot Sequence ---
        console.log('[SENTINEL-SYSTEM] Initializing Modules...');
        
        // Initialize Components
        if (typeof initCharts === 'function') initCharts();
        if (typeof initSentinelMap === 'function') initSentinelMap();
        
        // Initial Data Fetch
        if (typeof updateWeather === 'function') updateWeather(currentLat, currentLng);
        if (typeof updateTrendUI === 'function') updateTrendUI();
        if (typeof pollTelemetry === 'function') pollTelemetry();

        // Weather Refresh Cycle (10 Minutes)
        setInterval(() => {
            if (typeof updateWeather === 'function') updateWeather(currentLat, currentLng);
        }, 600000);
    });
</script>
