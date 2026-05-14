<!-- Public Dashboard Specific Logic -->
<script>
    function triggerReveal() {
        if(!document.body.classList.contains('loaded')) {
            document.body.classList.add('loaded'); 
        }
    }
    window.addEventListener('DOMContentLoaded', triggerReveal);
    window.addEventListener('load', triggerReveal);
    setTimeout(triggerReveal, 3000);

    setInterval(() => {
        const clockEl = document.getElementById('live-clock');
        if(clockEl) clockEl.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
    }, 1000);

    function toggleDeviceSelector() {
        const menu = document.getElementById('device-selector-menu'), icon = document.getElementById('device-selector-icon');
        if (menu.classList.contains('invisible')) {
            menu.classList.remove('invisible', 'scale-y-0', 'opacity-0'); menu.classList.add('scale-y-100', 'opacity-100'); icon.classList.add('rotate-180');
        } else {
            menu.classList.remove('scale-y-100', 'opacity-100'); menu.classList.add('scale-y-0', 'opacity-0'); icon.classList.remove('rotate-180');
            setTimeout(() => menu.classList.add('invisible'), 300);
        }
    }
    
    document.addEventListener('click', (e) => {
        const wrapper = document.getElementById('device-selector-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            const menu = document.getElementById('device-selector-menu');
            if (menu && !menu.classList.contains('invisible')) toggleDeviceSelector();
        }
    });

    function userSwitchDevice(slug, name, location, lat, lng) {
        document.getElementById('active-device-name').textContent = name;
        toggleDeviceSelector();
        if(typeof window.switchDevice === 'function') {
            window.switchDevice(slug, name, lat, lng);
            window.history.pushState({}, '', '/dashboard/' + slug);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const originalWeatherUpdate = window.updateWeatherWidget;
        if(originalWeatherUpdate) {
            window.updateWeatherWidget = function(data) {
                originalWeatherUpdate(data);
                const isRaining = data.weather[0].main.toLowerCase().includes('rain') || data.weather[0].description.toLowerCase().includes('hujan');
                const rainOverlay = document.getElementById('weather-rain');
                if(rainOverlay) isRaining ? rainOverlay.classList.add('active') : rainOverlay.classList.remove('active');
            }
        }
    });
</script>
