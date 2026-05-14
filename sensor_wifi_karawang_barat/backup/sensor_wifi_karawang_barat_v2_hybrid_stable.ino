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

  // 2. OTA UPDATE LOGIC
  server.on("/", []() {
    server.send(200, "text/plain",
                "WaterSense Active. Go to /update to flash new code.");
  });

  ElegantOTA.begin(&server); // Akses di http://[IP-NodeMCU]/update
  server.begin();
  Serial.println("HTTP Server & OTA Ready!");
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