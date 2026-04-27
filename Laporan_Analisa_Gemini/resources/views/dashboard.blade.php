@include('partials.dashboard.head')

<body class="min-h-screen relative antialiased selection:bg-blue-200 selection:text-blue-900 pb-10">

    <!-- Top Accent Line -->
    <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500 z-50"></div>

    <!-- Main Container -->
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 mt-6 relative z-10">
        
        <!-- Header Section -->
        @include('partials.dashboard.header')

        <!-- Sky Sentinel HERO HUD (Added for Enterprise Impact) -->
        <div id="hero-weather-container" class="mb-6">
            @include('partials.dashboard.analytics', ['showOnlyWeather' => true])
        </div>

        <!-- Dashboard Grid HUD -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Left Panel: Diagnostics & Logs -->
            @include('partials.dashboard.sidebar')

            <!-- Center Panel: The River View -->
            @include('partials.dashboard.main_kpi')

            <!-- Right Panel: Sentinel GIS Map -->
            @include('partials.dashboard.sentinel_map')

        </div>

        <!-- Bottom Section: Analytics & Charts -->
        @include('partials.dashboard.analytics', ['showOnlyWeather' => false])

    </div>

    <!-- Scripts Application -->
    @include('partials.dashboard.scripts')

</body>
</html>
