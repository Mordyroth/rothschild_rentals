<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 require "functions.php";

 $x = query_ezpass("SELECT * FROM supercharger");

 foreach ($x as $y) {
 	$dateStr = $y['date_and_time'];
	$dt = new DateTime($dateStr);
	$newDateStr = $dt->format("Y-m-d H:i:s");
	query_ezpass("UPDATE supercharger SET date_and_time = ? WHERE id = ?", $newDateStr, $y['id']);
	echo $newDateStr . "<br/>"; // Output: 2023-02-20 09:00:04
 }
?>