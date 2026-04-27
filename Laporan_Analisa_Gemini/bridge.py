import serial
import serial.tools.list_ports
import requests
import re
import time
import sys
import os
import statistics

# --- KONFIGURASI ---
BAUD_RATE = 115200
API_BASE_URL = 'http://127.0.0.1:8000/api'
DEVICE_SLUG = 'cybernova-s400-primary'

def auto_detect_port():
    """Auto-detect serial port based on common Arduino/IoT device signatures."""
    ports = serial.tools.list_ports.comports()
    for port in ports:
        desc = port.description.lower()
        hwid = port.hwid.lower()
        # Deteksi umum untuk Arduino, CH340, CP210x, dan perangkat USB Serial
        if "arduino" in desc or "ch340" in desc or "cp210" in desc or "usb" in desc or "usb" in hwid:
            return port.device
            
    # Fallback untuk penamaan khusus di Linux (ttyACM atau ttyUSB)
    for port in ports:
        if "ttyacm" in port.device.lower() or "ttyusb" in port.device.lower():
            return port.device
            
    # Fallback untuk Windows (COM)
    com_ports = [p.device for p in ports if "com" in p.device.lower()]
    if com_ports:
        return com_ports[-1] # Biasanya port yang terakhir ditambahkan adalah IoT
        
    return None

def notify_status(session, status):
    """Kirim status (online/offline) ke server Laravel."""
    url = f"{API_BASE_URL}/device/status"
    payload = {"slug": DEVICE_SLUG, "status": status}
    try:
        response = session.post(url, json=payload, timeout=5)
        if response.status_code == 200:
            print(f"[*] Server Notified: Device is {status.upper()}")
        else:
            print(f"[-] Failed to notify server: {response.status_code}")
    except Exception as e:
        print(f"[-] Connection Error to Server: {e}")

def main():
    print("="*60)
    print(f"   CYBERNOVA BRIDGE - REAL-TIME TELEMETRY SYSTEM")
    print("="*60)
    print(f"[*] Monitoring Port: AUTO-DETECT")
    
    ser = None
    last_status = None
    api_session = requests.Session()
    
    # --- STAGED PIPELINE CONFIG ---
    CYCLE_DURATION = 5.0
    SAMPLING_END = 3.0
    CALC_END = 4.0
    
    sample_buffer = []
    cycle_start_time = time.time()
    transmitted_this_cycle = False
    smoothed_value = None
    global_ema = None  # Variabel untuk Exponential Moving Average lintas siklus
    EMA_ALPHA = 0.3    # Faktor kehalusan (0.0 - 1.0). Makin kecil = makin halus tapi respon agak lambat
    string_buffer = "" # Menampung potongan teks dari serial agar tidak terpotong
    
    # Optimized regex to catch ANY signal (raw or final)
    pattern = re.compile(r'Jarak:\s*([\d.]+)\s*cm', re.IGNORECASE)

    while True:
        try:
            if ser is None:
                detected_port = auto_detect_port()
                if not detected_port:
                    if last_status != "offline":
                        print("[-] Waiting for Device... (Auto-Detecting Port)")
                        try:
                            notify_status(api_session, "offline")
                        except:
                            pass
                        last_status = "offline"
                    time.sleep(2)
                    continue
                    
                try:
                    ser = serial.Serial(detected_port, BAUD_RATE, timeout=0)
                    time.sleep(2)
                    print(f"\n[+] PORT {detected_port} CONNECTED!")
                    notify_status(api_session, "online")
                    last_status = "online"
                    cycle_start_time = time.time()
                except serial.SerialException:
                    if last_status != "offline":
                        print(f"[-] Connection failed to {detected_port}. Retrying...")
                        try:
                            notify_status(api_session, "offline")
                        except:
                            pass
                        last_status = "offline"
                    time.sleep(2)
                    continue

            # Timing Calculation
            elapsed = time.time() - cycle_start_time
            
            # --- PHASE 1: SAMPLING (0s - 3s) ---
            if elapsed < SAMPLING_END:
                transmitted_this_cycle = False
                if ser is not None:
                    # Capture entire buffer chunk
                    raw_chunk = ser.read_all().decode('utf-8', errors='ignore')
                    if raw_chunk:
                        string_buffer += raw_chunk
                        # Proses hanya jika ada baris baru (teks utuh)
                        while '\n' in string_buffer:
                            line, string_buffer = string_buffer.split('\n', 1)
                            matches = pattern.findall(line)
                            if matches:
                                for val in matches:
                                    dist = float(val)
                                    sample_buffer.append(dist)
                                    print(f"+", end="", flush=True)
                            elif line.strip():
                                # Cetak pesan error/peringatan dari Arduino jika ada
                                print(f"\n[!] INFO SENSOR: {line.strip()}")
                
                count = len(sample_buffer)
                if int(elapsed * 10) % 5 == 0: # Print every 0.5s
                    print(f"\r[T+{elapsed:.1f}s] PHASE 1: SAMPLING... [{count} samples] ", end="", flush=True)

            # --- PHASE 2: CALCULATING (3s - 4s) ---
            elif elapsed < CALC_END:
                if smoothed_value is None and len(sample_buffer) > 0:
                    # 1. Cari nilai tengah (Median) untuk membuang anomali ekstrem (misal tiba-tiba 0 atau 2000)
                    median_val = statistics.median(sample_buffer)
                    
                    # 2. Filter Ketat: Hanya ambil sampel yang mendekati median (Toleransi 3 cm)
                    # Ini menolak pantulan gelombang ultrasonik yang salah/melenceng
                    valid_samples = [x for x in sample_buffer if abs(x - median_val) <= 3.0]
                    if not valid_samples:
                        valid_samples = sample_buffer # Fallback jika semua menyebar
                        
                    # 3. Rata-rata dari nilai yang sudah bersih
                    batch_avg = sum(valid_samples) / len(valid_samples)
                    
                    # 4. Terapkan Exponential Moving Average (EMA) agar pergerakan sangat mulus
                    if global_ema is None:
                        global_ema = batch_avg
                    else:
                        global_ema = (EMA_ALPHA * batch_avg) + ((1 - EMA_ALPHA) * global_ema)
                        
                    smoothed_value = global_ema
                    print(f"\n[T+{elapsed:.1f}s] PHASE 2: CALCULATING... OK (Valid: {len(valid_samples)}/{len(sample_buffer)} | EMA: {smoothed_value:.2f}cm)")
                elif len(sample_buffer) == 0 and int(elapsed * 10) % 5 == 0:
                    print(f"\r[T+{elapsed:.1f}s] PHASE 2: WAITING FOR BATCH...", end="", flush=True)

            # --- PHASE 3: TRANSMITTING (4s - 5s) ---
            elif elapsed < CYCLE_DURATION:
                if not transmitted_this_cycle and smoothed_value is not None:
                    print(f"\n[T+{elapsed:.1f}s] PHASE 3: TRANSMITTING DATA...")
                    payload = {"distance": round(smoothed_value, 2), "valid_count": len(sample_buffer)}
                    try:
                        res = api_session.post(f"{API_BASE_URL}/sensor-data", json=payload, timeout=2.0)
                        if res.status_code == 200:
                            print(f"[*] DATA PUBLISHED: {smoothed_value:.2f}cm")
                        else:
                            print(f"[-] BROADCAST ERROR: {res.status_code}")
                    except Exception as e:
                        print(f"[-] SATELLITE FAIL: {str(e)[:15]}")
                    transmitted_this_cycle = True
                
                if int(elapsed * 10) % 5 == 0:
                    print(f"\r[T+{elapsed:.1f}s] PHASE 3: STABILIZING HUD...", end="", flush=True)

            # --- RESET CYCLE ---
            else:
                print(f"\n[!] CYCLE COMPLETE. RESETTING PIPELINE...")
                cycle_start_time = time.time()
                sample_buffer = []
                smoothed_value = None
                transmitted_this_cycle = False
                # JANGAN HAPUS string_buffer ATAU input_buffer!
                # Biarkan data yang masuk di Fase 2 & 3 terbaca di Fase 1 siklus berikutnya.

            time.sleep(0.001)

        except (serial.SerialException, OSError) as e:
            print(f"\n[!] PERANGKAT TERPUTUS: {e}")
            try:
                notify_status(api_session, "offline")
            except:
                pass
            last_status = "offline"
            if ser is not None:
                try:
                    ser.close()
                except:
                    pass
            ser = None
            print("[*] Mencoba menyambungkan kembali dalam 2 detik...")
            time.sleep(2)

        except KeyboardInterrupt:
            print("\n[*] Pembersihan sistem...")
            try:
                notify_status(api_session, "offline")
            except:
                pass
            if ser is not None:
                try:
                    ser.close()
                except:
                    pass
            api_session.close()
            sys.exit(0)
            
        except Exception as e:
            print(f"\n[-] Error Tak Terduga: {e}")
            time.sleep(1)

if __name__ == "__main__":
    main()
