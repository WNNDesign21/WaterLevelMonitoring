@include('partials.dashboard.head')

<style>
    /* DIRECT CACHE BYPASS CSS - ASSEMBLY SYSTEM */
    @keyframes revealFromLeft { 0% { opacity: 0; transform: translateX(-100px); filter: blur(20px); } 100% { opacity: 1; transform: translateX(0); filter: blur(0); } }
    @keyframes revealFromRight { 0% { opacity: 0; transform: translateX(100px); filter: blur(20px); } 100% { opacity: 1; transform: translateX(0); filter: blur(0); } }
    @keyframes revealFromBottom { 0% { opacity: 0; transform: translateY(100px); filter: blur(20px); } 100% { opacity: 1; transform: translateY(0); filter: blur(0); } }

    .v-reveal-left, .v-reveal-right, .v-reveal-bottom {
        opacity: 0; animation-duration: 1.5s; animation-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1); animation-fill-mode: forwards;
    }

    body.loaded .v-reveal-left { animation-name: revealFromLeft; }
    body.loaded .v-reveal-right { animation-name: revealFromRight; }
    body.loaded .v-reveal-bottom { animation-name: revealFromBottom; }

    body.loaded .delay-1 { animation-delay: 0.2s !important; }
    body.loaded .delay-2 { animation-delay: 0.6s !important; }
    body.loaded .delay-3 { animation-delay: 1.0s !important; }
    body.loaded .delay-4 { animation-delay: 1.4s !important; }
    body.loaded .delay-5 { animation-delay: 1.8s !important; }
    body.loaded .delay-6 { animation-delay: 2.2s !important; }
    body.loaded .delay-7 { animation-delay: 2.6s !important; }
    body.loaded .delay-8 { animation-delay: 3.0s !important; }
</style>

<body class="min-h-screen relative antialiased selection:bg-blue-200 selection:text-blue-900 pb-10 bg-slate-50 overflow-x-hidden">
    @include('partials.user_dashboard.welcome_portal')
    
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500 z-50"></div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 mt-4 relative z-10">
        @include('partials.dashboard.header')

        <!-- Unified Cockpit Interface -->
        <div class="glass-panel rounded-[2.5rem] p-4 lg:p-6 mt-4 bg-white/60 shadow-2xl backdrop-blur-2xl border border-white/80" style="perspective: 2500px;">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch transform-style-3d">
                @include('partials.user_dashboard.cockpit_left')
                @include('partials.user_dashboard.cockpit_center')
                @include('partials.user_dashboard.cockpit_right')
            </div>
        </div>
        
        @include('partials.user_dashboard.history_matrix')
    </div>

    @include('partials.dashboard.scripts')
    @include('partials.user_dashboard.user_scripts')
    @include('partials.dashboard.history_scripts')
</body>
</html>
