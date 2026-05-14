<!-- SENTINEL BASE CONFIGURATION & STATE -->
<script>
    // --- Configuration ---
    const SENSOR_ELEVATION_MDPL = 14.00; 
    const SIAGA_1_TMA = 13.00; // mdpl (1 meter dari sensor)
    const SIAGA_2_TMA = 12.80; // mdpl (1.2 meter dari sensor)
    const SIAGA_3_TMA = 12.50; // mdpl (1.5 meter dari sensor)
    const MAX_SAMPLES = 40;   
    
    // --- State ---
    let chartData = Array(MAX_SAMPLES).fill(0);
    let chartLabels = Array(MAX_SAMPLES).fill('');
    let previousDepth = 0;
    let lastRainIntensity = 0;
    let currentLat = {{ $primaryDevice->latitude ?? -6.2088 }};
    let currentLng = {{ $primaryDevice->longitude ?? 106.8456 }};
    
    let depthBuffer = []; 
    let lastVoiceTime = 0;
    let trendData24h = []; 
    let lastTrendUpdate = 0;
    let maxPeak = 0;
    let minBase = 9999;

    // --- DOM Selectors (Multi-instance safe helpers) ---
    const updateText = (selector, text) => {
        document.querySelectorAll(selector).forEach(el => {
            el.textContent = text;
            el.classList.remove('skeleton', 'w-24', 'h-10', 'w-32', 'h-3', 'w-16', 'h-8', 'h-14');
        });
    };
    const updateHtml = (selector, html) => {
        document.querySelectorAll(selector).forEach(el => {
            el.innerHTML = html;
            el.classList.remove('skeleton', 'w-24', 'h-10', 'w-32', 'h-3', 'w-16', 'h-8', 'h-14');
        });
    };
</script>
