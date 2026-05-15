<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    /**
     * Get current weather based on coordinates
     * This serves as a unified provider for both Web and Mobile
     */
    public function current(Request $request)
    {
        $lat = $request->input('lat', -6.2088);
        $lon = $request->input('lon', 106.8456);
        $lang = $request->input('lang', 'id');

        $apiKey = config('services.weather.key') ?? env('WEATHER_API_KEY', '9a77c5d39a304c8ba5670449261205');
        
        $cacheKey = "weather_{$lat}_{$lon}_{$lang}";
        
        $weather = Cache::remember($cacheKey, 900, function () use ($lat, $lon, $lang, $apiKey) {
            try {
                $response = Http::timeout(5)
                    ->retry(3, 100)
                    ->get("https://api.weatherapi.com/v1/current.json", [
                        'key' => $apiKey,
                        'q' => "$lat,$lon",
                        'lang' => 'id',
                        'aqi' => 'no'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'];
                    $condition = $current['condition'];
                    $conditionText = $condition['text'];
                    
                    $translations = [
                        'sunny' => 'Cerah', 'clear' => 'Cerah', 'partly cloudy' => 'Cerah Berawan',
                        'cloudy' => 'Berawan', 'overcast' => 'Mendung', 'mist' => 'Kabut',
                        'patchy rain possible' => 'Potensi Hujan', 'patchy snow possible' => 'Potensi Salju',
                        'patchy sleet possible' => 'Potensi Hujan Es', 'patchy freezing drizzle possible' => 'Potensi Gerimis Es',
                        'thundery outbreaks possible' => 'Potensi Petir', 'blowing snow' => 'Salju Tertiup',
                        'blizzard' => 'Badai Salju', 'fog' => 'Kabut Tebal', 'freezing fog' => 'Kabut Beku',
                        'patchy light drizzle' => 'Gerimis Ringan', 'light drizzle' => 'Gerimis',
                        'freezing drizzle' => 'Gerimis Es', 'heavy freezing drizzle' => 'Gerimis Es Lebat',
                        'patchy light rain' => 'Hujan Ringan', 'light rain' => 'Hujan Ringan',
                        'moderate rain at times' => 'Hujan Sedang Sesekali', 'moderate rain' => 'Hujan Sedang',
                        'heavy rain at times' => 'Hujan Lebat Sesekali', 'heavy rain' => 'Hujan Lebat',
                        'light freezing rain' => 'Hujan Es Ringan', 'moderate or heavy freezing rain' => 'Hujan Es Lebat',
                        'light sleet' => 'Hujan Es Ringan', 'moderate or heavy sleet' => 'Hujan Es Lebat',
                        'patchy light snow' => 'Salju Ringan', 'light snow' => 'Salju Ringan',
                        'patchy moderate snow' => 'Salju Sedang', 'moderate snow' => 'Salju Sedang',
                        'patchy heavy snow' => 'Salju Lebat', 'heavy snow' => 'Salju Lebat',
                        'ice pellets' => 'Hujan Es Batu', 'light rain shower' => 'Hujan Rintik',
                        'moderate or heavy rain shower' => 'Hujan Lebat', 'torrential rain shower' => 'Hujan Badai',
                        'light sleet showers' => 'Hujan Es Ringan', 'moderate or heavy sleet showers' => 'Hujan Es Lebat',
                        'light snow showers' => 'Salju Ringan', 'moderate or heavy snow showers' => 'Salju Lebat',
                        'light showers of ice pellets' => 'Hujan Es Batu Ringan', 'moderate or heavy showers of ice pellets' => 'Hujan Es Batu Lebat',
                        'patchy light rain with thunder' => 'Hujan Petir Ringan', 'moderate or heavy rain with thunder' => 'Hujan Petir Lebat',
                        'patchy light snow with thunder' => 'Salju Petir Ringan', 'moderate or heavy snow with thunder' => 'Salju Petir Lebat'
                    ];

                    $lowerText = strtolower(trim($conditionText));
                    if (isset($translations[$lowerText])) {
                        $conditionText = $translations[$lowerText];
                    }

                    return [
                        'temp_c' => $current['temp_c'],
                        'wind_kph' => $current['wind_kph'],
                        'humidity' => $current['humidity'],
                        'condition' => [
                            'text' => $conditionText,
                            'code' => $condition['code'],
                            'icon' => $condition['icon'],
                        ],
                        'location' => $data['location']['name'],
                        'region' => $data['location']['region'],
                        'provider' => 'WaterSense Unified Weather'
                    ];
                }
                return null;
            } catch (\Exception $e) {
                \Log::error("Weather API Connection Error: " . $e->getMessage());
                return null;
            }
        });
        if (!$weather) {
            return response()->json(['error' => 'Unable to fetch weather data'], 500);
        }

        return response()->json($weather);
    }
}