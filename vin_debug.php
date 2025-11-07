<?php
$vin = "WF0SXXGBWS9Y12345"; // <- VIN testowy
$apiKey = "4e2c8f8b0302";
$secretKey = "f0af9472e3";
$id = "decode";

// Oblicz kontrolną sumę
$control = substr(sha1("$vin|$id|$apiKey|$secretKey"), 0, 10);
$url = "https://api.vincario.com/3.2/$id/$apiKey/$control/$vin.json";

echo "<h2>🔗 Pełny URL:</h2><pre>$url</pre>";

// Wykonaj zapytanie CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "<p style='color:red;'>Błąd CURL: $error</p>";
} else {
    echo "<h2>📦 Odpowiedź API:</h2><pre>$response</pre>";
}
?>
