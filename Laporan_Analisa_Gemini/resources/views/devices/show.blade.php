@include('partials.devices.head')

<body class="min-h-screen py-8 px-4 md:py-16 md:px-0">

    <div class="max-w-[1000px] mx-auto">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 space-x-2 text-xs font-bold uppercase tracking-widest text-slate-400">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">DASBOR</a>
            <span>/</span>
            <a href="{{ route('devices.index') }}" class="hover:text-blue-600 transition-colors">PERANGKAT</a>
            <span>/</span>
            <span class="text-slate-600">{{ $device->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left: Device Image & Status -->
            @include('partials.devices.hero')

            <!-- Right: Technical Details & Telemetry -->
            <div class="lg:col-span-7 space-y-8">
                @include('partials.devices.summary')
                @include('partials.devices.telemetry')
                @include('partials.devices.components')
            </div>

        </div>

        <!-- Interactive Geotagging -->
        @include('partials.devices.geotagging')

    </div>

    <!-- Scripts application -->
    @include('partials.devices.scripts')

</body>
</html>
