#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <WiFiClient.h>

// ================= KONFIGURASI =================
const char *ssid = "OPPO K3";
const char *password = "12312345";
const char *serverName = "http://10.178.144.143:8000/api/sensor-data";
const String deviceSlug = "node-mcu-cp2102-johar-69f819289fa67";
// ===============================================

const int trigPin = 5; // D1
const int echoPin = 4; // D2

void setup() {
  Serial.begin(115200);
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(LED_BUILTIN, OUTPUT);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi OK!");
}

void loop() {
  // 1. Trigger Pulse (20us agar lebih kuat)
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(20);
  digitalWrite(trigPin, LOW);

  // 2. Read Pulse
  long duration = pulseIn(echoPin, HIGH, 100000);
  float distance = (duration > 0) ? (duration * 0.034 / 2)
                                  : 0.33; // Heartbeat 0.33 jika gagal

  // 3. Send to Server
  WiFiClient client;
  HTTPClient http;
  http.begin(client, serverName);
  http.addHeader("Content-Type", "application/json");
  String json = "{\"device_slug\":\"" + deviceSlug +
                "\", \"distance\":" + String(distance) +
                ", \"valid_count\": 1}";

  int code = http.POST(json);
  Serial.print("Kirim ");
  Serial.print(distance);
  Serial.print(" cm, Code: ");
  Serial.println(code);

  if (code > 0)
    digitalWrite(LED_BUILTIN, LOW);
  else
    digitalWrite(LED_BUILTIN, HIGH);

  http.end();
  delay(5000); // Tunggu 5 detik persis seperti sebelumnya
}
