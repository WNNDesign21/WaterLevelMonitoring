<!-- SENTINEL WEATHER SATELLITE -->
<script>
    window.updateWeather = async function(lat, lng) {
        const WEATHER_KEY = "{{ config('services.openweather.key') }}";
        const locationPromise = resolveLocation(lat, lng);

        if(!WEATHER_KEY || WEATHER_KEY === 'OPENWEATHER_API_KEY') {
            updateText('#sky-desc', 'SATELLITE KEY MISSING');
            return;
        }

        try {
            const cacheKey = `sentinel_weather_${lat.toFixed(4)}_${lng.toFixed(4)}`;
            const cached = localStorage.getItem(cacheKey);
            if(cached) {
                const data = JSON.parse(cached);
                if(Date.now() - data.ts < 600000) {
                    applyWeatherData(data.weather);
                    await locationPromise;
                    return;
                }
            }

            const loc = await locationPromise;
            const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lng}&appid=${WEATHER_KEY}&units=metric&lang=id`;
            const res = await fetch(url);
            const apiData = await res.json();

            if(apiData.main) {
                const weatherObj = {
                    temp: apiData.main.temp,
                    feels: apiData.main.feels_like,
                    humidity: apiData.main.humidity,
                    wind: apiData.wind.speed * 3.6,
                    pressure: apiData.main.pressure,
                    desc: apiData.weather[0].description,
                    main: apiData.weather[0].main,
                    ts: Date.now()
                };
                localStorage.setItem(cacheKey, JSON.stringify({ts: Date.now(), weather: weatherObj}));
                applyWeatherData(weatherObj);
            }
        } catch (e) { 
            updateText('#sky-desc', 'SIGNAL DISTURBANCE');
        }
    }

    function applyWeatherData(w) {
        const weatherIcons = {
            'Clear': '<i class="fa-solid fa-sun text-amber-400"></i>',
            'Clouds': '<i class="fa-solid fa-cloud-sun text-amber-400"></i>',
            'Rain': '<i class="fa-solid fa-cloud-showers-heavy text-blue-400"></i>',
            'Drizzle': '<i class="fa-solid fa-cloud-rain text-cyan-400"></i>',
            'Thunderstorm': '<i class="fa-solid fa-cloud-bolt text-indigo-400"></i>',
            'Snow': '<i class="fa-solid fa-snowflake text-slate-100"></i>',
            'Atmosphere': '<i class="fa-solid fa-smog text-slate-300"></i>'
        };

        const glowColors = {
            'Clear': 'bg-amber-400/20', 'Clouds': 'bg-blue-400/10', 'Rain': 'bg-indigo-600/20', 
            'Thunderstorm': 'bg-purple-600/20', 'Drizzle': 'bg-cyan-600/20'
        };

        updateHtml('#sky-icon-main', weatherIcons[w.main] || '<i class="fa-solid fa-cloud text-slate-400"></i>');
        updateText('#sky-temp', `${w.temp.toFixed(1)}°C`);
        updateText('#sky-feels', `${Math.round(w.feels)}°`);
        updateText('#sky-desc', w.desc.toUpperCase());
        updateHtml('#sky-wind', `${w.wind.toFixed(1)} <span class="text-[10px] text-slate-400">KM/H</span>`);
        updateHtml('#sky-humidity', `${w.humidity.toFixed(0)} <span class="text-[10px] text-slate-400">%</span>`);
        updateHtml('#sky-pressure', `${w.pressure.toFixed(0)} <span class="text-[10px] text-slate-400">HPA</span>`);

        document.querySelectorAll('#weather-bg-glow').forEach(el => {
            el.className = `absolute top-0 right-0 w-96 h-96 rounded-full blur-[100px] -mr-48 -mt-48 transition-all duration-1000 ${glowColors[w.main] || 'bg-slate-400/10'}`;
        });
        lastRainIntensity = (w.main === 'Rain' || w.main === 'Drizzle') ? 10 : 0; 
    }
</script>
