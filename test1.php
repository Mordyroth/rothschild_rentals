<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "functions.php";

$x = add_ezpass_and_supercharger();
?>
<pre><?php print_r($x) ?></pre>