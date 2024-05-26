<?php

ini_set('display_errors',1);ini_set('display_startup_errors',1);error_reporting(E_ALL);
require '/var/www/html/rothschild_rentals/functions.php';
// Now you can use any function or variable defined in functions.php
require_once 'vendor/autoload.php';

use Twilio\TwiML\VoiceResponse;

$response = new VoiceResponse();

$text = query("SELECT * FROM system_text WHERE name = ?", "main_menu")[0]['text1'];
$gather = $response->gather([
    'action' => 'handle/index.php',  // The URL to which the user's input should be sent.
    'method' => 'POST',  // Use POST or GET based on your preference.
    'timeout' => 10,  // Wait 10 seconds for user input.
    'numDigits' => 1  // Expect 4 digits from the user, adjust as needed.
]);
$gather->say($text);
$response->say('We didn\'t receive any input.');
$response->redirect('index.php');

header('Content-Type: text/xml');
echo $response;

?>
