<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(10000);
require 'functions.php';

$cars = query_ezpass("SELECT car_nickname FROM car_info");
$nicknamesArray = array_column($cars, 'car_nickname');


?>
<pre>
	<?php print_r($nicknamesArray) ?>
</pre>