<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Monitoring Dashboard | Real-Time Water Level</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        cyber: {
                            base: '#0f172a',
                            surface: '#1e293b',
                            primary: '#06b6d4',
                            accent: '#3b82f6',
                            glow: '#0ea5e9',
                            danger: '#ef4444'
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'ping-slow': 'ping 2s cubic-bezier(0, 0, 0.2, 1) infinite',
                        'wave-slow': 'wave 10s linear infinite',
                        'wave-fast': 'wave 7s linear infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'scan': 'scan 3s linear infinite',
                    },
                    keyframes: {
                        wave: {
                            '0%': { transform: 'translateX(0)' },
                            '100%': { transform: 'translateX(-50%)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        scan: {
                            '0%': { transform: 'translateY(-100%)', opacity: '0' },
                            '50%': { opacity: '0.5' },
                            '100%': { transform: 'translateY(100%)', opacity: '0' }
                        }
                    }
                }
            }
        }
    </script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Pusher & Echo -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <!-- Leaflet JS for GIS Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Import Google Fonts: Inter and Rajdhani -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Luxury Minimalist Light Theme Colors */
            --luxury-primary: #3b82f6;      /* Bright Blue */
            --luxury-secondary: #0ea5e9;    /* Light Blue */
            --luxury-accent: #06b6d4;       /* Cyan */
            --luxury-bg: #f8fafc;           /* Slate 50 - Off white */
            --luxury-surface: rgba(255, 255, 255, 0.85);
            --luxury-border: rgba(226, 232, 240, 0.8);
            --luxury-text-main: #0f172a;    /* Slate 900 */
            --luxury-text-muted: #64748b;   /* Slate 500 */
            --wave-1: rgba(14, 165, 233, 0.4);
            --wave-2: rgba(59, 130, 246, 0.6);
            --wave-3: rgba(6, 182, 212, 0.8);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--luxury-bg);
            color: var(--luxury-text-main);
            background-image: radial-gradient(circle at top right, rgba(59, 130, 246, 0.05), transparent 40%),
                              radial-gradient(circle at bottom left, rgba(6, 182, 212, 0.05), transparent 40%);
        }

        /* Glassmorphism Panel */
        .glass-panel {
            background: var(--luxury-surface);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--luxury-border);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08), 
                        0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }
        
        /* Enterprise Luxury Profiles */
        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.01),
                0 10px 15px -3px rgba(0, 0, 0, 0.02),
                0 20px 25px -5px rgba(0, 0, 0, 0.02);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-panel:hover {
            box-shadow: 
                0 10px 20px -5px rgba(0, 0, 0, 0.03),
                0 25px 50px -12px rgba(59, 130, 246, 0.08);
            transform: translateY(-3px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .font-rajdhani { font-family: 'Rajdhani', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        .font-mono-sentinel { font-family: 'Fira Code', 'Roboto Mono', monospace; }
        
        .text-gradient {
            background: linear-gradient(135deg, var(--luxury-primary), var(--luxury-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Animated River Cross-Section */
        .river-container {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(248, 250, 252, 0.2) 0%, rgba(226, 232, 240, 0.9) 100%);
            border-radius: 1.5rem;
            overflow: visible;
            border: 2px solid rgba(226, 232, 240, 0.5);
            box-shadow: inset 0 0 30px rgba(0,0,0,0.05);
            margin-top: 1rem;
        }

        /* River Banks (Luxury Aesthetic) */
        .river-bank-left, .river-bank-right {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 15%;
            background: linear-gradient(90deg, var(--luxury-bg) 0%, rgba(248, 250, 252, 0.5) 100%);
            z-index: 10;
            border-right: 1px solid var(--luxury-border);
            box-shadow: 5px 0 15px rgba(0,0,0,0.05);
        }
        .river-bank-right {
            right: 0;
            left: auto;
            background: linear-gradient(270deg, var(--luxury-bg) 0%, rgba(248, 250, 252, 0.5) 100%);
            border-right: none;
            border-left: 1px solid var(--luxury-border);
            box-shadow: -5px 0 15px rgba(0,0,0,0.05);
        }

        /* The River Water Container */
        .river-water {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 0%; /* Dynamic */
            transition: height 2.5s cubic-bezier(0.2, 0.8, 0.2, 1), background-image 1s ease, box-shadow 1s ease;
            overflow: hidden;
            z-index: 10;
            border-radius: 0 0 1rem 1rem;
            
            /* Clean Glowing Top Edge */
            border-top: 3px solid rgba(255, 255, 255, 0.9);
            
            /* Gradient shift animation */
            background-size: 200% 100%;
            animation: liquid-flow 15s ease-in-out infinite alternate;
            
            /* Dynamic State - Fed by JS variables (--r, --g, --b) */
            --r: 14; --g: 165; --b: 233; /* Default Blue */
            --dr: 2; --dg: 132; --db: 199; /* Default Darker Blue */
            
            background-image: linear-gradient(90deg, 
                rgba(var(--r), var(--g), var(--b), 1) 0%, 
                rgba(var(--dr), var(--dg), var(--db), 1) 50%, 
                rgba(var(--r), var(--g), var(--b), 1) 100%
            );
            box-shadow: 0 -4px 30px rgba(var(--dr), var(--dg), var(--db), 0.5), inset 0 20px 60px rgba(255, 255, 255, 0.4);
        }
        .river-water::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 15px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.5) 0%, transparent 100%);
            animation: surface-pulse 4s ease-in-out infinite;
        }

        .level-line {
            position: absolute;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(0,0,0,0.03);
            z-index: 5;
        }
        /* Accessibility Patterns */
        .pattern-overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.1;
            z-index: 5;
        }
        .pattern-siaga1 { background-image: repeating-linear-gradient(45deg, #000 0, #000 1px, transparent 0, transparent 50%); background-size: 10px 10px; }
        .pattern-siaga2 { background-image: repeating-linear-gradient(-45deg, #000 0, #000 1px, transparent 0, transparent 50%); background-size: 8px 8px; }
        .pattern-siaga3 { background-image: radial-gradient(#000 1px, transparent 0); background-size: 6px 6px; }

        .surface-badge {
            position: absolute;
            right: 0%;
            background: #0f172a;
            color: #fff;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            border: 2px solid #fff;
            z-index: 100;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        .level-label {
            position: absolute;
            right: 12%;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: currentColor;
            z-index: 45;
            transition: all 0.4s ease;
            filter: drop-shadow(0 2px 0 rgba(255,255,255,1));
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .level-label.active {
            opacity: 1 !important;
            transform: translateX(-15px) scale(1.2);
            filter: drop-shadow(0 0 15px rgba(255,255,255,0.8));
        }
        /* Weather Sentinel Styles */
        #weather-sentinel {
            z-index: 20;
            border: 1px solid rgba(255,255,255,0.4);
        }
        #weather-correlation-alert {
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        @keyframes pulse-blue {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 20px 5px rgba(59, 130, 246, 0.2); }
        }
        .animate-pulse-slow {
            animation: pulse-blue 3s infinite;
        }

        /* Sky Sentinel (Luxury Weather) Styles */
        @keyframes float {
            0%, 100% { transform: translateY(0); filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1)); }
            50% { transform: translateY(-10px); filter: drop-shadow(0 20px 40px rgba(59, 130, 246, 0.2)); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .depth-tick {
            position: absolute;
            left: 17%;
            width: 14px;
            height: 3px;
            background: #0f172a;
            z-index: 45;
            border-radius: 2px;
            border: 1px solid rgba(255,255,255,0.8);
        }
        .depth-value {
            position: absolute;
            left: 5%;
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
            font-family: 'Rajdhani', sans-serif;
            z-index: 45;
            background: rgba(255,255,255,0.8);
            padding: 0 4px;
            border-radius: 4px;
        }

        @keyframes wave-ripple {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        /* Custom Scrollbar for Logs */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--luxury-secondary);
            border-radius: 4px;
        }

        /* Radar dot glow override */
        #radar-blip {
            box-shadow: 0 0 10px var(--luxury-primary);
        }
        
        /* Sentinel Map Styles */
        #sentinel-map {
            width: 100%;
            height: 100%;
            border-radius: 1.5rem;
            z-index: 1;
            filter: grayscale(0.2) contrast(1.1);
        }
        
        /* Map Pulse Animation */
        .sentinel-pulse {
            width: 12px;
            height: 12px;
            background: var(--luxury-primary);
            border-radius: 50%;
            box-shadow: 0 0 0 rgba(59, 130, 246, 0.4);
            animation: pulse-ring 2s infinite;
        }

        .sentinel-pulse.danger {
            background: #ef4444;
            box-shadow: 0 0 0 rgba(239, 68, 68, 0.4);
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }

        .leaflet-container {
            background: #0f172a !important; /* Dark theme background */
        }
        
        .sentinel-info-window {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px;
            color: white;
            min-width: 200px;
        }
    </style>
</head>
