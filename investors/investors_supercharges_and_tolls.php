<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//set_time_limit(10000);
require 'functions.php';

$cars = query_ezpass("SELECT * FROM car_info");

$current_month = date('m');
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

$charges = query_ezpass("SELECT * FROM supercharger WHERE (date_and_time >= ? AND date_and_time < ?) AND match_status = ? ORDER BY date_and_time DESC", $start_date, $end_date, "not matched");


foreach ($cars as $car) {
    unset($results);
    $total = 0;
    $plate = $car['plate'];
    $tag = $car['ezpass'];
    $vin = $car['vin'];
    

    $rows = query_ezpass("SELECT * FROM ezpass WHERE transaction_format_date >= ? AND transaction_format_date < ? AND matched = ? AND match_status = ? ORDER BY transaction_format_date DESC", $start_date, $end_date, $vin, "matched_to_investor");

    //echo $car['car_nickname'] . "<br/>";

    foreach ($rows as $row) {
        //echo "Transaction Date: " . $row['transaction_date'] . "<br>";
       // echo "Transaction Format Date: " . $row['transaction_format_date'] . "<br>";
       // echo "Tag/Plate: " . $row['tag_plate'] . "<br>";
        $decimal = (float)preg_replace('/[^0-9.]/', '', $row['amount']);
        //echo "Amount: " . $decimal . "<br/>";
        $total += $decimal;
        // Add more columns here as needed
       // echo "<hr>";

        $row_data = [
            'car_nickname' => $car['car_nickname'],
            'transaction_date' => $row['transaction_date'],
            'transaction_format_date' => $row['transaction_format_date'],
            'tag_plate' => $row['tag_plate'],
            'amount' => $decimal,
            'vin' => $vin,
            'type' => "toll",
            'row_id' => $row['id']
        ];
        $results[] = $row_data; // Append the row data to the results array
    }

    $charges = query_ezpass("SELECT * FROM supercharger WHERE (date_and_time >= ? AND date_and_time < ?) AND match_status = ? AND matched = ? ORDER BY date_and_time DESC", $start_date, $end_date, "matched_to_investor", $vin);
    //echo "<pre>";
   // print_r($charges);
    //echo "</pre>";

    if (!empty($charges)) {
        foreach ($charges as $charge) {
            $total += $charge['amount'];
            $row_data = [
                'car_nickname' => $car['car_nickname'],
                'transaction_date' => $charge['date_and_time'],
                'amount' => $charge['amount'],
                'location' => $charge['location'],
                'vin' => $vin,
                'type' => "supercharger",
                'row_id' => $charge['id']
            ];
            $results[] = $row_data;
        }
    }
    echo "<pre>";
    if (!empty($results)) {
        print_r($results);
    }
    echo "</pre>";
   // echo "<br/>";
    echo $car['car_nickname'] . "<br/>";
    echo "total: " . $total;
   // echo "<br/>";
    //echo "<br/>";
   // echo "<br/>";
}

?>