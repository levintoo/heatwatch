#include "DHT.h"

#define DHTPIN 2         // Digital pin connected to the DHT sensor
#define DHTTYPE DHT11    // DHT 11

DHT dht(DHTPIN, DHTTYPE);

void setup() {
  Serial.begin(9600);
  // Serial.println(F("DHT11 - Celsius Only"));
  dht.begin();
}

void loop() {
  delay(5000); // Wait between measurements

  float h = dht.readHumidity();
  float t = dht.readTemperature(); // Temperature in °C

  if (isnan(h) || isnan(t)) {
    Serial.println(F("Failed to read from DHT sensor!"));
    return;
  }

  float hic = dht.computeHeatIndex(t, h, false); // Heat index in °C

  // Serial.print(F("Humidity: "));
  // Serial.print(h);
  // Serial.print(F("%  Temperature: "));
  // Serial.print(t);
  // Serial.print(F("°C  Heat index: "));
  // Serial.print(hic);
  // Serial.println(F("°C"));

  String json = "{";
  json += "\"humidity\": ";  // Key for humidity
  json += h;                 // Value of humidity
  json += ", \"temperature\": ";  // Key for temperature
  json += t;                 // Value of temperature
  json += ", \"heat_index\": ";  // Key for heat index
  json += hic;               // Value of heat index
  json += "}";

  Serial.println(json);
}
