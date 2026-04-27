<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $device->name }} | Detail Perangkat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Leaflet JS for Interactive Geotagging -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        :root {
            --luxury-primary: #3b82f6;
            --luxury-bg: #f8fafc;
            --luxury-surface: rgba(255, 255, 255, 0.9);
            --luxury-border: rgba(226, 232, 240, 1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--luxury-bg);
            background-image: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.03) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.03) 0%, transparent 40%);
        }
        .glass-panel {
            background: var(--luxury-surface);
            backdrop-filter: blur(20px);
            border: 1px solid var(--luxury-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
        .tech-card {
            border-left: 4px solid var(--luxury-primary);
        }

        /* Interactive Map Styles */
        #interactive-map {
            width: 100%;
            height: 350px;
            border-radius: 1.5rem;
            z-index: 1;
        }
        .leaflet-container {
            background: #f1f5f9 !important;
        }
        .custom-pin {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pin-marker {
            width: 16px;
            height: 16px;
            background: var(--luxury-primary);
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
