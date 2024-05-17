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
function getData(string $url): string
{
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
}

$cityName = trim((string)readline(("Enter city name: \n")));
if (empty($cityName)) {
    exit("City name cannot be empty. Please enter a valid city name.\n");
}
$cityName = urlencode($cityName);

$appid = getenv("API_KEY");
if (empty($appid)) {
    exit("API key is not set. Please set your API key and try again.\n");
}

$geoUrl = "http://api.openweathermap.org/geo/1.0/direct?q=$cityName&appid=$appid";

$jsonGeo = getData($geoUrl);
$jsonDataGeo = json_decode($jsonGeo);

$lat = $jsonDataGeo[0]->lat;
$lon = $jsonDataGeo[0]->lon;
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$appid&units=metric";

$jsonWeather = getData($weatherUrl);
$jsonDataWeather = json_decode($jsonWeather);

if (empty($jsonGeo) || empty($jsonWeather)) {
    exit("Failed to fetch weather data. Please try again later.\n");
}
if ($jsonDataGeo === null || !isset($jsonDataGeo[0]->lat) || !isset($jsonDataGeo[0]->lon)) {
    exit("Failed to parse geo data. Please try again later.\n");
}
if ($jsonDataWeather === null || !isset($jsonDataWeather->main) || !isset($jsonDataWeather->weather[0]->main) || !isset($jsonDataWeather->wind) || !isset($jsonDataWeather->main->humidity)) {
    exit("Failed to parse weather data. Please try again later.\n");
}

$dayTime = date("l", $jsonDataWeather->dt);
$cityName = $jsonDataGeo[0]->name;
$temperature = round($jsonDataWeather->main->temp);
$weatherMain = $jsonDataWeather->weather[0]->main;
$wind = round($jsonDataWeather->wind->speed);
$humidity = $jsonDataWeather->main->humidity;

echo "\e[37m$dayTime, weather in \e[93m$cityName\n";
echo "\t\e[97m$weatherMain\n";
echo "\e[37mTemperature:\t\e[97m$temperature °C,\n";
echo "\e[37mWind:\t\t\e[97m$wind m/s,\n";
echo "\e[94mHumidity:\t\e[97m$humidity%.\n";