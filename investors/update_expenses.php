<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'functions.php';

$car_nickname = $_GET['car_nickname'];
//$transaction_date = $_GET['transaction_date'];
$transaction_format_date = $_GET['transaction_format_date'];
//$tag_plate = $_GET['tag_plate'];
$amount = $_GET['amount'];
$vin = $_GET['vin'];

if ($_GET['update'] == "bill_to_investor") {
	if ($_GET['type'] == "supercharger") {
		query_ezpass("UPDATE supercharger SET matched = ?, match_status = ? WHERE id = ?", $_GET['vin'], "matched_to_investor", $_GET['row_id']);
	} else if ($_GET['type'] == "toll") {
	 	echo "Success, updated " . $car_nickname . ". button: " .  $_GET['row_id'];
		query_ezpass("UPDATE ezpass SET matched = ?, match_status = ? WHERE id = ?", $_GET['vin'], "matched_to_investor", $_GET['row_id']);
	}
}

?>