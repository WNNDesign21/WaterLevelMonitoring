#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>
#include <ESP8266WiFi.h>
#include <ElegantOTA.h> // Library: ElegantOTA by ayushsharma82
#include <WiFiClient.h>
#include <WiFiManager.h> // Library: WiFiManager by tablatronix

// ================= KONFIGURASI STATIS =================
const char *serverName = "http://103.172.205.35/api/sensor-data";
const String deviceSlug = "node-wifi-wemos-d1-69f01e5649f84";
const int trigPin = 5; // D1
const int echoPin = 4; // D2

unsigned long previousMillis = 0;
const long interval = 5000; // Interval kirim data 5 detik

ESP8266WebServer server(80);

void setup() {
  Serial.begin(115200);
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(LED_BUILTIN, OUTPUT);

  // 1. WIFI MANAGER LOGIC
  WiFiManager wm;
  // wm.resetSettings(); // Buka komentar ini jika ingin hapus WiFi tersimpan

  // Mencoba konek ke WiFi yang tersimpan
  if (!wm.autoConnect("WaterSense_Setup")) {
    Serial.println("Gagal konek. Restarting...");
    delay(3000);
    ESP.restart();
  }

  // --- FORCE HYBRID MODE (AP + STA) ---
  // Agar hotspot tetap aktif walaupun sudah konek ke internet
  WiFi.mode(WIFI_AP_STA);
  WiFi.softAP("WaterSense_Node_KRWBAR01", "NodeKRWBAR@01"); 
  // ------------------------------------

  Serial.println("\nWiFi Connected! IP Address:");
  Serial.println(WiFi.localIP());
  Serial.print("Access Point Active: WaterSense_Node_KRWBAR01\n");

  // 2. WEB SERVER & DASHBOARD LOGIC
  // Halaman Utama (Dashboard dengan Login)
  server.on("/", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) {
      return server.requestAuthentication();
    }
    
    String html = "<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    html += "<style>body{background:#0f172a;color:#f8fafc;font-family:sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0}";
    html += ".glass{background:rgba(255,255,255,0.05);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.1);padding:2rem;border-radius:2rem;width:90%;max-width:400px;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5)}";
    html += "h1{font-size:1.5rem;font-weight:900;margin-bottom:1.5rem;letter-spacing:-0.05em;color:#3b82f6}";
    html += ".data-card{background:rgba(255,255,255,0.03);padding:1.5rem;border-radius:1.5rem;margin:1rem 0}";
    html += ".val{font-size:3rem;font-weight:900;color:#60a5fa;margin:0.5rem 0}";
    html += ".label{font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94a3b8}";
    html += ".btn{display:block;width:100%;padding:1rem;background:#3b82f6;color:white;text-decoration:none;border-radius:1rem;font-weight:800;font-size:0.8rem;text-transform:uppercase;margin-top:2rem;transition:0.3s}";
    html += ".btn:hover{background:#2563eb;transform:translateY(-2px)}</style></head><body>";
    html += "<div class='glass'><h1>WaterSense KRWBAR01</h1>";
    html += "<div class='data-card'><div class='label'>Tinggi Air</div><div class='val' id='dist'>--</div><div class='label'>Centimeter</div></div>";
    html += "<div class='data-card'><div class='label'>Kualitas Sinyal</div><div class='val' style='font-size:1.5rem' id='rssi'>--</div></div>";
    html += "<a href='/update' class='btn'>Update Firmware</a></div>";
    html += "<script>setInterval(()=>{fetch('/api/data').then(r=>r.json()).then(d=>{";
    html += "document.getElementById('dist').innerText=d.dist.toFixed(1);";
    html += "document.getElementById('rssi').innerText=d.rssi+' dBm';";
    html += "})}, 2000);</script></body></html>";
    
    server.send(200, "text/html", html);
  });

  // API untuk AJAX Dashboard
  server.on("/api/data", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) return server.send(401);
    long duration = pulseIn(echoPin, HIGH, 100000);
    float distance = (duration > 0) ? (duration * 0.034 / 2) : 0.33;
    String json = "{\"dist\":" + String(distance) + ",\"rssi\":" + String(WiFi.RSSI()) + "}";
    server.send(200, "application/json", json);
  });

  ElegantOTA.begin(&server, "admin", "NodeKRWBAR@01"); // Proteksi OTA dengan login
  server.begin();
  Serial.println("Secure Dashboard & OTA Ready!");
}

void loop() {
  server.handleClient(); // WAJIB: Menjaga dashboard & OTA tetap responsif
  ElegantOTA.loop();

  unsigned long currentMillis = millis();

  // 3. NON-BLOCKING INTERVAL (Pengganti delay)
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    sendSensorData();
  }
}

void sendSensorData() {
  // Trigger Pulse
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(20);
  digitalWrite(trigPin, LOW);

  long duration = pulseIn(echoPin, HIGH, 100000);
  float distance = (duration > 0) ? (duration * 0.034 / 2) : 0.33;

  WiFiClient client;
  HTTPClient http;

  if (http.begin(client, serverName)) {
    http.addHeader("Content-Type", "application/json");
    String json = "{\"device_slug\":\"" + deviceSlug +
                  "\", \"distance\":" + String(distance) +
                  ", \"valid_count\": 1}";

    int code = http.POST(json);
    Serial.printf("Kirim: %.2f cm, Status: %d\n", distance, code);

    if (code > 0)
      digitalWrite(LED_BUILTIN, LOW);
    else
      digitalWrite(LED_BUILTIN, HIGH);

    http.end();
  }
}