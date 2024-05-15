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
function getData(string $url): stdClass {
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content  = curl_exec($ch);
    curl_close($ch);
    $contentDecoded = json_decode($content);

    if (is_array($contentDecoded) && count($contentDecoded) === 1) {
        return $contentDecoded[0];
    }
    return $contentDecoded;
};

$appid = getenv("API_KEY");
$cityName = (string)readline(("Enter city name: \n"));
$geoUrl = "http://api.openweathermap.org/geo/1.0/direct?q=$cityName&appid=$appid";
$jsonDataGeo = getData($geoUrl);
$lat = $jsonDataGeo->lat;
$lon = $jsonDataGeo->lon;

$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$appid&units=metric";
$jsonDataWeather = getData($weatherUrl);

$dayTime = date("l", $jsonDataWeather->dt);
$cityName = $jsonDataGeo->name;
$temperature = round($jsonDataWeather->main->temp);
$weatherMain = $jsonDataWeather->weather[0]->main;
$wind = round($jsonDataWeather->wind->speed);
$humidity = $jsonDataWeather->main->humidity;

echo "\e[37m$dayTime, weather in \e[93m$cityName\n";
echo "\t\e[97m$weatherMain\n";
echo "\e[37mTemperature:\t\e[97m$temperature °C,\n";
echo "\e[37mWind:\t\t\e[97m$wind m/s,\n";
echo "\e[94mHumidity:\t\e[97m$humidity%.\n";