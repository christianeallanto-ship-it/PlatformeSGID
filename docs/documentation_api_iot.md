# 📡 Documentation de l'API IoT — BENINCLEAN

Cette documentation est destinée au développeur chargé de concevoir le système électronique (ESP32 / Arduino / PlatformIO) afin d'envoyer les mesures des capteurs à la plateforme en ligne.

---

## 🛠️ Spécifications de la Requête

*   **URL de l'API** : `https://christianeapps.alwaysdata.net/api/bins/telemetry`
*   **Méthode HTTP** : `POST`
*   **Sécurité (CSRF)** : Exemptée (aucune clé ou jeton CSRF requis pour cette route).
*   **En-têtes requis (Headers)** :
    *   `Content-Type: application/json`
    *   `Accept: application/json`

---

## 📦 Corps de la Requête (Payload JSON)

Le corps de la requête doit être un objet JSON valide contenant les champs suivants :

| Paramètre | Type | Rôle | Contraintes |
| :--- | :--- | :--- | :--- |
| `code` | `string` | Le code unique d'identification du bac | **Obligatoire**. Format libre (ex: `"B141"`, `"B142"`) |
| `fill_level` | `integer` | Le pourcentage de remplissage mesuré | **Obligatoire**. Entier entre `0` (vide) et `100` (plein). |
| `temperature` | `float` | La température ambiante mesurée en °C | *Optionnel / Nullable*. Décimal (ex: `29.8`) |
| `air_quality` | `integer` | La concentration en CO2 / gaz (ppm) | *Optionnel / Nullable*. Entier (ex: `400`) |
| `latitude` | `float` | La coordonnée de latitude GPS du bac | *Optionnel / Nullable*. Décimal (ex: `6.365214`) |
| `longitude` | `float` | La coordonnée de longitude GPS du bac | *Optionnel / Nullable*. Décimal (ex: `2.418632`) |

### Exemple de JSON Complet :
```json
{
    "code": "B141",
    "fill_level": 75,
    "temperature": 29.8,
    "air_quality": 400,
    "latitude": 6.365214,
    "longitude": 2.418632
}
```

---

## 🔌 Fonctionnement Intelligent (Plug & Play)

Pour faciliter le déploiement sur le terrain et suite à un nettoyage de la base de données :
*   **Si le bac existe déjà** : Ses données (niveau de remplissage et statut) sont mises à jour.
*   **Si le bac n'existe pas** : Le serveur le **crée automatiquement** dans la base de données. Il sera automatiquement placé à des coordonnées géographiques aléatoires dans la ville d'intervention active (ex: Cotonou, Porto-Novo, etc.) et s'affichera directement sur la carte de monitoring.

---

## 🖥️ Exemples de Connexion

### 1. Exemple avec cURL (Terminal / Command Line)
```bash
curl -X POST https://christianeapps.alwaysdata.net/api/bins/telemetry \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"code":"B141", "fill_level":75}'
```

### 2. Exemple de Code C++ (Arduino / ESP32)
Voici un exemple de code C++ standard à utiliser sur une carte ESP32 équipée d'un capteur ultrasonique :

```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h> // Installer la bibliothèque "ArduinoJson"

const char* ssid = "VOTRE_WIFI_SSID";
const char* password = "VOTRE_WIFI_MOT_DE_PASSE";
const char* serverUrl = "https://christianeapps.alwaysdata.net/api/bins/telemetry";

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connexion au WiFi...");
  }
  Serial.println("Connecté au WiFi !");
}

void sendTelemetry(String binCode, int fillLevel) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");

    // Création du document JSON
    StaticJsonDocument<200> doc;
    doc["code"] = binCode;
    doc["fill_level"] = fillLevel;

    String requestBody;
    serializeJson(doc, requestBody);

    // Envoi de la requête POST
    int httpResponseCode = http.POST(requestBody);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.print("Code HTTP : ");
      Serial.println(httpResponseCode);
      Serial.println("Réponse : " + response);
    } else {
      Serial.print("Erreur d'envoi HTTP : ");
      Serial.println(httpResponseCode);
    }
    http.end();
  }
}

void loop() {
  // Remplacer 75 par la valeur mesurée par votre capteur ultrasonique
  //sendTelemetry("B141", 75);
  
{
  "code": "B141",
  "fill_level": 35,
  "temperature": 29.81,
  "air_quality": 400,
  "latitude": 6.365214,
  "longitude": 2.418632
}

  // Attendre 5 minutes avant la prochaine mesure (300 000 ms)
  delay(300000);
}
```
