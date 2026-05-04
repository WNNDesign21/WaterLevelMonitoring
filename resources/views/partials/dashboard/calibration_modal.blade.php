<!-- Calibration Modal -->
<div id="calibrationModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-[2.5rem] bg-white shadow-2xl transition-all border border-slate-100 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 p-8 text-white shrink-0">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight">Kalibrasi Sensor</h3>
                        <p class="text-blue-100 text-xs mt-1 font-medium tracking-widest uppercase opacity-80">Device Configuration</p>
                    </div>
                    <button onclick="closeCalibrationModal()" class="bg-white/20 hover:bg-white/30 rounded-full p-2 transition-colors">
                        <i class="fa-solid fa-xmark w-5 h-5 flex items-center justify-center"></i>
                    </button>
                </div>
            </div>

            <!-- Form & Visual Body -->
            <div class="flex flex-col md:flex-row flex-1 overflow-y-auto bg-slate-50/50">
                
                <!-- Kiri: Realtime Visualizer -->
                <div class="w-full md:w-5/12 p-8 border-b md:border-b-0 md:border-r border-slate-200 flex flex-col items-center justify-center bg-white relative min-h-[400px]">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 absolute top-6 left-6">Visualisasi Proporsi</h4>
                    
                    <div class="relative w-full flex flex-col items-center mt-8 px-12">
                        <!-- Sensor Module -->
                        <div class="w-16 h-8 bg-slate-800 rounded-lg shadow-lg relative z-20 flex items-center justify-center border-b-4 border-cyan-400">
                            <i class="fa-solid fa-satellite-dish text-cyan-400 text-xs"></i>
                        </div>
                        
                        <!-- Sensor to Bank Gap -->
                        <div id="vis-gap-container" class="w-full flex justify-center relative transition-all duration-300 ease-out" style="height: 100px;">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 text-[9px] font-mono font-bold text-slate-500 text-right pr-4 w-1/2 whitespace-nowrap">
                                <span id="vis-gap-val">100</span> cm<br>
                                <span class="text-[8px] uppercase tracking-wider text-slate-400">Jarak Bantaran</span>
                            </div>
                            <div class="w-px h-full bg-dashed border-l-2 border-dashed border-slate-300"></div>
                            <!-- Measurement Arrow -->
                            <div class="absolute right-[20%] top-0 bottom-0 flex flex-col items-center justify-between py-1">
                                <i class="fa-solid fa-caret-up text-slate-300 text-[8px]"></i>
                                <div class="w-px h-full bg-slate-300"></div>
                                <i class="fa-solid fa-caret-down text-slate-300 text-[8px]"></i>
                            </div>
                        </div>

                        <!-- Bank Line -->
                        <div class="w-full h-1 bg-slate-300 relative z-10 flex items-center">
                            <div class="absolute right-full mr-2 text-[8px] font-bold text-slate-400 uppercase whitespace-nowrap">Bibiran</div>
                            <div class="w-full border-t border-slate-400 border-dashed opacity-50"></div>
                        </div>

                        <!-- River / Tank -->
                        <div id="vis-tank-container" class="w-24 border-x-4 border-b-4 border-slate-200 rounded-b-xl relative transition-all duration-300 ease-out bg-blue-50/50" style="height: 150px;">
                            <div class="absolute right-full pr-4 top-1/2 -translate-y-1/2 text-[9px] font-mono font-bold text-blue-500 text-right whitespace-nowrap">
                                <span id="vis-tank-val">100</span> cm<br>
                                <span class="text-[8px] uppercase tracking-wider text-blue-400">Kedalaman Air</span>
                            </div>
                            <!-- Fake Water Level -->
                            <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-blue-500/30 to-cyan-400/20 rounded-b-lg border-t-2 border-blue-400/50 flex flex-col items-center justify-center">
                                <i class="fa-solid fa-water text-blue-500/50 text-xs mb-1"></i>
                                <span class="text-[8px] font-bold text-blue-600/50 uppercase">Area Air</span>
                            </div>
                        </div>
                        
                        <!-- Elevation Label -->
                        <div class="mt-6 text-[10px] font-bold text-slate-500 bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-mountain text-slate-400"></i>
                            <span>Elevasi: <span id="vis-elevation-val" class="font-mono text-blue-600 font-black">14.00</span> MDPL</span>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Form -->
                <div class="w-full md:w-7/12 p-8 flex flex-col">
                    <form id="calibrationForm" class="space-y-6 flex-1 flex flex-col">
                        @csrf
                        <input type="hidden" name="device_slug" id="input_device_slug">
                        
                        <div class="flex-1 space-y-6">
                            <!-- Elevation MDPL -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Ketinggian Sensor (MDPL)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-mountain text-blue-500 opacity-50"></i>
                                    </div>
                                    <input type="number" step="0.01" name="elevation_mdpl" id="input_elevation" required
                                        class="block w-full pl-11 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm outline-none"
                                        placeholder="Contoh: 14.00">
                                </div>
                            </div>

                            <!-- Sensor to Bank -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jarak Sensor ke Bantaran (cm)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-ruler-vertical text-blue-500 opacity-50"></i>
                                    </div>
                                    <input type="number" name="sensor_to_bank" id="input_sensor_to_bank" required
                                        class="block w-full pl-11 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm outline-none"
                                        placeholder="Contoh: 100">
                                </div>
                            </div>

                            <!-- River Depth -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kedalaman Sungai (cm)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-water text-blue-500 opacity-50"></i>
                                    </div>
                                    <input type="number" name="river_depth" id="input_river_depth" required
                                        class="block w-full pl-11 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm outline-none"
                                        placeholder="Contoh: 100">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="saveCalibrationBtn"
                            class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs tracking-[0.2em] uppercase hover:bg-blue-600 shadow-xl shadow-slate-900/10 transition-all flex items-center justify-center space-x-2 mt-8">
                            <span>Simpan Konfigurasi</span>
                            <i class="fa-solid fa-check ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Info Footer -->
            <div class="px-8 py-3 bg-white border-t border-slate-100 shrink-0">
                <p class="text-[10px] text-slate-400 font-medium text-center italic">
                    Perubahan akan langsung berdampak pada visualisasi Web dan Mobile.
                </p>
            </div>
        </div>
    </div>
</div>
