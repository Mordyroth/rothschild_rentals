<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../functions.php';

/*$current_month = date('m');
$current_year = date('Y');

if ($current_month == 1) {
    $previous_month = 12;
    $previous_year = $current_year - 1;
} else {
    $previous_month = $current_month - 1;
    $previous_year = $current_year;
}

$start_date = $previous_year . '-' . str_pad($previous_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00';
$end_date = $current_year . '-' . str_pad($current_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00';

$expenses = query("SELECT * FROM expenses WHERE (date_and_time >= ? AND date_and_time < ?) AND car = ? ORDER BY date_and_time DESC", $start_date, $end_date, "Green T");
*/

$x = query("SELECT * FROM reservations LIMIT 1");
?>
<pre><?php print_r($x) ?></pre>