<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SYSTEM_ACCESS_RESTRICTED | WaterSense</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #020617; overflow: hidden; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .glow { text-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
        .shield-pulse { animation: pulse 3s infinite; }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; filter: drop-shadow(0 0 10px rgba(239, 68, 68, 0.4)); }
            50% { transform: scale(1.05); opacity: 1; filter: drop-shadow(0 0 30px rgba(239, 68, 68, 0.6)); }
            100% { transform: scale(1); opacity: 0.8; filter: drop-shadow(0 0 10px rgba(239, 68, 68, 0.4)); }
        }
        .grid-bg {
            background-image: linear-gradient(rgba(15, 23, 42, 0.8) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(15, 23, 42, 0.8) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen text-slate-200">
    <!-- Matrix Grid Background -->
    <div class="absolute inset-0 grid-bg opacity-40"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-slate-950/50 to-slate-950"></div>

    <div class="relative z-10 text-center px-6 max-w-2xl">
        <!-- Security Icon -->
        <div class="mb-12 inline-block">
            <div class="w-32 h-32 rounded-full bg-red-500/10 border-2 border-red-500/20 flex items-center justify-center shield-pulse">
                <i class="fa-solid fa-shield-halved text-6xl text-red-500"></i>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="flex items-center justify-center space-x-3 mb-6">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
            <span class="mono text-[10px] font-bold text-red-500 uppercase tracking-[0.5em]">SYSTEM_EMERGENCY_LOCKDOWN</span>
        </div>

        <!-- Main Heading -->
        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter leading-none mb-6">
            Sistem Sedang <span class="text-red-500 underline decoration-red-500/30">Dibatasi</span>
        </h1>

        <!-- Description -->
        <p class="text-slate-400 text-lg font-medium leading-relaxed mb-12">
            Mohon maaf atas ketidaknyamanannya. Protokol keamanan pusat sedang diaktifkan. 
            <span class="text-white font-bold block mt-2">Silakan dicoba kembali secara berkala.</span>
        </p>

        <!-- Technical Footnote -->
        <div class="inline-block px-8 py-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl">
            <div class="flex flex-col items-center space-y-2">
                <span class="mono text-[10px] text-slate-500 uppercase tracking-widest">Protocol_Code: 503_SERVICE_RESTRICTED</span>
                <span class="mono text-[10px] text-slate-400">Time_Stamp: {{ now()->format('Y-m-d H:i:s') }} WIB</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-16">
            <p class="text-[9px] font-black text-slate-600 uppercase tracking-[0.3em]">&copy; 2026 WaterSense Sentinel Command Center</p>
        </div>
    </div>

    <!-- Scanline Effect -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden opacity-5">
        <div class="w-full h-1 bg-white animate-scanline"></div>
    </div>

    <style>
        @keyframes scanline {
            0% { transform: translateY(-100vh); }
            100% { transform: translateY(100vh); }
        }
        .animate-scanline {
            animation: scanline 8s linear infinite;
        }
    </style>
</body>
</html>
