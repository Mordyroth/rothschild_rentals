<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

// Retrieve user input from the 'Digits' POST parameter.
$userInput = $_POST['Digits'];

$response = new VoiceResponse();

// Handle the input. For demonstration purposes, we'll just say back the digits.
$response->say("You entered: " . $userInput);

header('Content-Type: text/xml');
echo $response;

?>