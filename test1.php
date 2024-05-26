<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "functions.php";
$nickname = "Elnt Gray H16";
$x = car_info($nickname);

?>
<pre><?php print_r($x) ?></pre>