<?php

require_once "config.php";

$url = "https://api.openweathermap.org/data/2.5/weather?q=Pune&appid="
     . WEATHER_API_KEY
     . "&units=metric";

$response = file_get_contents($url);

echo "<pre>";
echo htmlspecialchars($response);
echo "</pre>";

?>