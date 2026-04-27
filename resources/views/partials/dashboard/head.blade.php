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
                        'fade-in-up': 'fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        'fade-in-up': {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
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
    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
        
        /* Luxury Glass Tank Animation */
        .glass-tank-container {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(255,255,255, 0.1) 0%, rgba(255,255,255, 0.6) 100%);
            border-radius: 2rem;
            overflow: hidden; /* Contains the water */
            border: 2px solid rgba(255, 255, 255, 0.8);
            box-shadow: inset 0 0 20px rgba(0,0,0,0.05), 
                        inset 0 20px 40px rgba(255,255,255, 0.8),
                        0 10px 30px rgba(0,0,0,0.05);
            margin-top: 1rem;
            backdrop-filter: blur(5px);
        }

        /* Glare effect for glass tank */
        .glass-tank-container::after {
            content: '';
            position: absolute;
            top: 0; left: 5%; bottom: 0; width: 15%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transform: skewX(-20deg);
            z-index: 50;
            pointer-events: none;
        }

        /* The Liquid Container */
        .liquid-water {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 0%; /* Dynamic */
            transition: height 2.5s cubic-bezier(0.2, 0.8, 0.2, 1);
            z-index: 10;
            
            background: linear-gradient(180deg, #38bdf8 0%, #0369a1 40%, #1e3a8a 100%);
            box-shadow: inset 0 20px 60px rgba(255, 255, 255, 0.2),
                        0 -10px 40px rgba(14, 165, 233, 0.3);
            border-top: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Inner Bubbles / Particles */
        .liquid-water-particles {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.4) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
            animation: rise-up 20s linear infinite;
            pointer-events: none;
        }

        /* Waves using CSS mask-image */
        .liquid-water::before {
            content: '';
            position: absolute;
            top: -19px; /* Sit right on top of the liquid */
            left: -5%;
            width: 110%; /* Slightly wider to allow swaying */
            height: 20px;
            background-color: #38bdf8;
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,119.56,189.7,100.8,236.4,85.87,281.42,71.21,321.39,56.44Z'/%3E%3C/svg%3E");
            -webkit-mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.08,130.83,119.56,189.7,100.8,236.4,85.87,281.42,71.21,321.39,56.44Z'/%3E%3C/svg%3E");
            mask-size: 100% 100%;
            mask-repeat: no-repeat;
            animation: wave-sway 6s ease-in-out infinite alternate;
        }

        .liquid-water::after {
            content: '';
            position: absolute;
            top: -24px;
            left: -5%;
            width: 110%;
            height: 25px;
            background-color: rgba(2, 132, 199, 0.8);
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z'/%3E%3C/svg%3E");
            -webkit-mask-size: 100% 100%;
            -webkit-mask-repeat: no-repeat;
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z'/%3E%3C/svg%3E");
            mask-size: 100% 100%;
            mask-repeat: no-repeat;
            animation: wave-sway-reverse 8s ease-in-out infinite alternate;
        }

        @keyframes wave-sway {
            0% { transform: translateX(-2%); }
            100% { transform: translateX(2%); }
        }
        @keyframes wave-sway-reverse {
            0% { transform: translateX(2%); }
            100% { transform: translateX(-2%); }
        }

        /* Cockpit Assembly Animations */
        @keyframes assembleBg {
            0% { opacity: 0; box-shadow: none; border-color: transparent; backdrop-filter: blur(0px); }
            100% { opacity: 1; }
        }
        .animate-assemble-bg {
            opacity: 0;
            animation: assembleBg 1.5s ease-out forwards;
        }

        @keyframes assembleLeft {
            0% { opacity: 0; transform: translateX(-150px) translateZ(-50px) scale(0.9); }
            100% { opacity: 1; transform: translateX(0) translateZ(0) scale(1); }
        }
        .animate-assemble-left {
            opacity: 0;
            transform-style: preserve-3d;
            animation: assembleLeft 1.2s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards;
        }

        @keyframes assembleCenter {
            0% { opacity: 0; transform: translateY(150px) translateZ(50px) scale(0.9); }
            100% { opacity: 1; transform: translateY(0) translateZ(0) scale(1); }
        }
        .animate-assemble-center {
            opacity: 0;
            transform-style: preserve-3d;
            animation: assembleCenter 1.2s cubic-bezier(0.16, 1, 0.3, 1) 0.6s forwards;
        }

        @keyframes assembleRight {
            0% { opacity: 0; transform: translateX(150px) translateZ(-50px) scale(0.9); }
            100% { opacity: 1; transform: translateX(0) translateZ(0) scale(1); }
        }
        .animate-assemble-right {
            opacity: 0;
            transform-style: preserve-3d;
            animation: assembleRight 1.2s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards;
        }

        /* Immersive Weather (Rain) */
        .weather-rain {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 50;
            overflow: hidden;
            border-radius: 2.5rem;
            opacity: 0;
            transition: opacity 2s ease;
        }
        .weather-rain.active { opacity: 1; }
        .drop {
            position: absolute;
            bottom: 100%;
            width: 2px;
            height: 40px;
            pointer-events: none;
            animation: drop 0.5s linear infinite;
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0.4));
        }
        @keyframes drop {
            0% { transform: translateY(0vh); }
            100% { transform: translateY(100vh); }
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
