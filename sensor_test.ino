void setup() {
  Serial.begin(115200); // TAMBAHKAN INI
  pinMode(5, OUTPUT);   // D1
  pinMode(4, INPUT);    // D2
  Serial.println("\n--- Sistem Siap: Memulai Pengujian JSN-SR04T ---");
}

void loop() {
  digitalWrite(5, LOW);
  delayMicroseconds(2);
  digitalWrite(5, HIGH);
  delayMicroseconds(10);
  digitalWrite(5, LOW);

  long duration = pulseIn(4, HIGH, 30000); // Timeout 30ms
  int distance = duration * 0.034 / 2;

  if (duration == 0) {
    Serial.println("Error: Tidak ada pantulan (Cek VCC/Wiring!)");
  } else {
    Serial.print("Jarak: ");
    Serial.print(distance);
    Serial.println(" cm");
  }
  delay(500);
}