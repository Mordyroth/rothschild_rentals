<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//set_time_limit(10000);
require 'functions.php';
$x = query_ezpass("UPDATE ezpass SET matched = ? AND match_status = ? WHERE id = ?", "5tyhnxnsbcsahc", "matched", "9721");

?>
<pre><?php print_r($x) ?></pre>