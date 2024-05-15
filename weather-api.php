<?php
/*
 * Create an application that allows you to enter city and/or country included,
 * to display current weather situation in that city.
 * Use https://openweathermap.org/api API to gather the information.
 * Explore the API and how to access the data.
 * - Acquire API key that will allow you to get the data
 * - Use PHP in-built methods to gather the data from the API (DO NOT USE GUZZLE/Packigist) for now
 */

// TODO export API_KEY=yourownkey
// TODO before running php weather-api.php

$appid = getenv("API_KEY");
$cityName = (string)readline(("Enter city name: \n"));
$stateCode = "";
$countryCode = "";

$geoUrl = "http://api.openweathermap.org/geo/1.0/direct?q=$cityName,$stateCode,$countryCode&appid=$appid";

$jsonGeo = file_get_contents($geoUrl);
$jsonDataGeo = json_decode($jsonGeo);

$lat = $jsonDataGeo[0]->lat;
$lon = $jsonDataGeo[0]->lon;
$url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$appid}&units=metric";

$ch = curl_init($url);
$fp = fopen("weather.json", "w");
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
if (curl_error($ch)) {
    fwrite($fp, curl_error($ch));
}
curl_close($ch);
fclose($fp);

$jsonWeather = file_get_contents('weather.json');
$jsonDataWeather = json_decode($jsonWeather);

$dayTime = date("l", $jsonDataWeather->dt);
$cityName = $jsonDataGeo[0]->name;
$temperature = round($jsonDataWeather->main->temp);
$weatherMain = $jsonDataWeather->weather[0]->main;
$wind = round($jsonDataWeather->wind->speed);
$humidity = $jsonDataWeather->main->humidity;

echo "\e[37m$dayTime, weather in \e[93m$cityName.\n";
echo "\t\e[97m$weatherMain\n";
echo "\e[37mTemperature:\t\e[97m$temperature °C,\n";
echo "\e[37mWind:\t\t\e[97m$wind m/s,\n";
echo "\e[94mHumidity:\t\e[97m$humidity%.\n";