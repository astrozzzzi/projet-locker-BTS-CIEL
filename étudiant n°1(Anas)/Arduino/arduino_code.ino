#include <SPI.h>
#include <Ethernet.h>

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
IPAddress ip(192, 168, 1, 100);

EthernetServer server(80);

String request = "";

// prototypes
String extractValue(String req, String key);

void setup() {
  Serial.begin(9600);

  Ethernet.begin(mac, ip);
  server.begin();

  Serial.println("Arduino API ready 🚀");
}

void loop() {
  EthernetClient client = server.available();

  if (client) {

    request = "";
    bool currentLineIsBlank = true;

    while (client.connected()) {
      if (client.available()) {
        char c = client.read();
        request += c;

        if (c == '\n' && currentLineIsBlank) break;

        if (c == '\n') currentLineIsBlank = true;
        else if (c != '\r') currentLineIsBlank = false;
      }
    }

    // 🔍 extraction
    String phone = extractValue(request, "phone=");
    String name  = extractValue(request, "name=");

    if (phone.length() > 0) {

      if (name.length() == 0) name = "Inconnu";

      Serial.print("📞 ");
      Serial.print(name);
      Serial.print(" : ");
      Serial.println(phone);

    } else {
      Serial.println("❌ Aucun numero detecte");
    }

    client.println("HTTP/1.1 200 OK");
    client.println("Content-Type: text/plain");
    client.println("Connection: close");
    client.println();
    client.println("OK");

    delay(1);
    client.stop();
  }
}
// =========================
// 👉 FONCTION ICI (hors loop)
// =========================
String extractValue(String req, String key) {

  int start = req.indexOf(key);

  if (start == -1) return "";

  String value = req.substring(start + key.length());

  int end1 = value.indexOf('&');
  int end2 = value.indexOf(' ');
  int end3 = value.indexOf('\n');

  int end = value.length();

  if (end1 != -1 && end1 < end) end = end1;
  if (end2 != -1 && end2 < end) end = end2;
  if (end3 != -1 && end3 < end) end = end3;

  return value.substring(0, end);

}
