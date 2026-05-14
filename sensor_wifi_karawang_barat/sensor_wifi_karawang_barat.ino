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

// --- FUNGSI ROBUST DISTANCE (MEDIAN FILTER) ---
float getRobustDistance() {
  const int samples = 5;
  float distances[samples];
  
  for (int i = 0; i < samples; i++) {
    digitalWrite(trigPin, LOW);
    delayMicroseconds(2);
    digitalWrite(trigPin, HIGH);
    delayMicroseconds(20);
    digitalWrite(trigPin, LOW);
    
    long duration = pulseIn(echoPin, HIGH, 100000);
    distances[i] = (duration > 0) ? (duration * 0.034 / 2) : 0.33;
    delay(30);
  }

  // Bubble Sort
  for (int i = 0; i < samples - 1; i++) {
    for (int j = 0; j < samples - i - 1; j++) {
      if (distances[j] > distances[j + 1]) {
        float temp = distances[j];
        distances[j] = distances[j + 1];
        distances[j + 1] = temp;
      }
    }
  }
  return distances[samples / 2];
}

void sendSensorData() {
  float distance = getRobustDistance();
  WiFiClient client;
  HTTPClient http;

  if (http.begin(client, serverName)) {
    http.addHeader("Content-Type", "application/json");
    String json = "{\"device_slug\":\"" + deviceSlug +
                  "\", \"distance\":" + String(distance) +
                  ", \"valid_count\": 5}";

    int code = http.POST(json);
    Serial.printf("Kirim (Robust): %.2f cm, Status: %d\n", distance, code);

    if (code > 0) digitalWrite(LED_BUILTIN, LOW);
    else digitalWrite(LED_BUILTIN, HIGH);

    http.end();
  }
}

void setup() {
  Serial.begin(115200);
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(LED_BUILTIN, OUTPUT);

  WiFiManager wm;
  if (!wm.autoConnect("WaterSense_Setup")) {
    Serial.println("Gagal konek. Restarting...");
    delay(3000);
    ESP.restart();
  }

  WiFi.mode(WIFI_AP_STA);
  WiFi.softAP("WaterSense_Node_KRWBAR01", "NodeKRWBAR@01"); 

  Serial.println("\nWiFi Connected! IP Address:");
  Serial.println(WiFi.localIP());
  Serial.print("Access Point Active: WaterSense_Node_KRWBAR01\n");

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
    html += "<div class='glass'>";
    html += "<img src='http://103.172.205.35/assets/img/logo/WaterSenseIcon.png' style='width:60px;margin-bottom:1rem'>";
    html += "<h1>WaterSense KRWBAR01</h1>";
    html += "<div class='data-card'><div class='label'>Tinggi Air</div><div class='val' id='dist'>--</div><div class='label'>Centimeter</div></div>";
    html += "<div class='data-card'><div class='label'>Kualitas Sinyal</div><div class='val' style='font-size:1.5rem' id='rssi'>--</div></div>";
    html += "<a href='/wifi' class='btn' style='background:#10b981;margin-bottom:1rem'>WiFi Management OS</a>";
    html += "<a href='/update' class='btn' style='background:#64748b'>Update Firmware</a></div>";
    html += "<script>setInterval(()=>{fetch('/api/data').then(r=>r.json()).then(d=>{";
    html += "document.getElementById('dist').innerText=d.dist.toFixed(1);";
    html += "document.getElementById('rssi').innerText=d.rssi+' dBm';";
    html += "})}, 2000);</script></body></html>";
    server.send(200, "text/html", html);
  });

  server.on("/wifi", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) return server.requestAuthentication();
    String html = "<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    html += "<style>body{background:#0f172a;color:#f8fafc;font-family:sans-serif;padding:1rem;display:flex;justify-content:center} .glass{background:rgba(255,255,255,0.05);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.1);padding:1.5rem;border-radius:1.5rem;width:100%;max-width:400px;text-align:center} h1{font-size:1.2rem;color:#3b82f6;margin-bottom:1.5rem} .card{background:rgba(255,255,255,0.03);padding:1rem;border-radius:1rem;margin-bottom:1rem;text-align:left} .btn{display:inline-block;padding:0.6rem 1rem;background:#3b82f6;color:white;text-decoration:none;border-radius:0.7rem;font-size:0.8rem;font-weight:700;border:none;cursor:pointer;margin-right:0.5rem} .net-item{display:flex;justify-content:space-between;align-items:center;padding:0.8rem;border-bottom:1px solid rgba(255,255,255,0.05)}</style></head><body>";
    html += "<div class='glass'>";
    html += "<img src='http://103.172.205.35/assets/img/logo/WaterSenseIcon.png' style='width:50px;margin-bottom:1rem'>";
    html += "<h1>WiFi Management OS</h1>";
    html += "<div class='card'><div style='font-size:0.7rem;text-transform:uppercase;color:#94a3b8;margin-bottom:0.5rem'>Status Saat Ini</div>";
    html += "<div style='font-weight:800'>" + String(WiFi.status() == WL_CONNECTED ? WiFi.SSID() : "Terputus") + "</div>";
    html += "<div style='margin-top:1rem'><button onclick='wifiAction(\"disconnect\")' class='btn' style='background:#f59e0b'>Putus</button>";
    html += "<button onclick='wifiAction(\"forget\")' class='btn' style='background:#ef4444'>Lupakan</button></div></div>";
    html += "<div class='card'><button onclick='scanWiFi()' class='btn' id='scanBtn'>Pindai Jaringan</button>";
    html += "<div id='networks' style='margin-top:1rem;font-size:0.9rem'></div></div>";
    html += "<a href='/' style='color:#94a3b8;text-decoration:none;font-size:0.8rem'>&larr; Kembali ke Dashboard</a></div>";
    html += "<script>function wifiAction(a){ if(confirm(\"Yakin?\")){ fetch(\"/api/wifi/\"+a).then(r=>r.text()).then(t=>{alert(t);location.reload();}); } }";
    html += "function scanWiFi(){ const b=document.getElementById(\"scanBtn\"); b.innerText=\"Memindai...\"; b.disabled=true;";
    html += "fetch(\"/api/wifi/scan\").then(r=>r.json()).then(d=>{ let h=\"\"; d.forEach(n=>{ h+=`<div class='net-item'><span>${n.s} (${n.r}dBm)</span><button onclick='connect(\"${n.s}\")' class='btn' style='padding:0.3rem 0.6rem'>Konek</button></div>`; }); document.getElementById(\"networks\").innerHTML=h; b.innerText=\"Pindai Ulang\"; b.disabled=false; }); }";
    html += "function connect(s){ const p=prompt(\"Password untuk \"+s); if(p!==null){ fetch(`/api/wifi/connect?s=${encodeURIComponent(s)}&p=${encodeURIComponent(p)}`).then(r=>r.text()).then(t=>alert(t)); } }</script></body></html>";
    server.send(200, "text/html", html);
  });

  server.on("/api/wifi/scan", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) return server.send(401);
    int n = WiFi.scanNetworks();
    String json = "[";
    for (int i = 0; i < n; ++i) {
      json += "{\"s\":\"" + WiFi.SSID(i) + "\",\"r\":" + String(WiFi.RSSI(i)) + "}";
      if (i < n - 1) json += ",";
    }
    json += "]";
    server.send(200, "application/json", json);
  });

  server.on("/api/wifi/connect", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) return server.send(401);
    String s = server.arg("s"); String p = server.arg("p");
    server.send(200, "text/plain", "Mencoba menyambungkan ke " + s + "...");
    WiFi.begin(s.c_str(), p.c_str());
  });

  server.on("/api/wifi/disconnect", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) return server.send(401);
    WiFi.disconnect();
    server.send(200, "text/plain", "Koneksi diputus.");
  });

  server.on("/api/wifi/forget", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) return server.send(401);
    WiFi.disconnect(true);
    server.send(200, "text/plain", "Jaringan dilupakan. Alat akan restart...");
    delay(2000); ESP.restart();
  });

  server.on("/api/data", []() {
    if (!server.authenticate("admin", "NodeKRWBAR@01")) return server.send(401);
    float distance = getRobustDistance();
    String json = "{\"dist\":" + String(distance) + ",\"rssi\":" + String(WiFi.RSSI()) + "}";
    server.send(200, "application/json", json);
  });

  ElegantOTA.begin(&server, "admin", "NodeKRWBAR@01");
  server.begin();
  Serial.println("Secure Dashboard & OTA Ready!");
}

void loop() {
  server.handleClient();
  ElegantOTA.loop();
  unsigned long currentMillis = millis();
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    sendSensorData();
  }
}