<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//set_time_limit(10000);
require 'functions.php';

$cars = query_ezpass("SELECT * FROM car_info");

$current_month = date('m');
$current_year = date('Y');

if ($current_month <= 2) {
    $two_months_ago_month = $current_month + 10; // If it's January or February, we go to November or December of the previous year.
    $two_months_ago_year = $current_year - 1;
} else {
    $two_months_ago_month = $current_month - 2;
    $two_months_ago_year = $current_year;
}

$start_date = $two_months_ago_year . '-' . str_pad($two_months_ago_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00';
$end_date = date('Y-m-d H:i:s'); // This will be the current date and time.

$no_car_match = query_ezpass("SELECT * FROM ezpass WHERE (transaction_format_date >= ? AND transaction_format_date < ?) AND (match_status = ? OR match_status = ?) AND description != ? ORDER BY transaction_format_date DESC", $start_date, $end_date, "not matched", "not tested", "Prepaid Payment");

foreach ($no_car_match as $no_match) {
    $x = $no_match['tag_plate'];
    $exists = false;

    foreach ($cars as $childArray) {
        if ($childArray["ezpass"] === $x || $childArray["plate"] === $x) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        // The value of x doesn't exist in any 'ezpass' or 'plate' field.
        $no_match_results[] = $no_match;
    } 
}

// this code will give the previous month so if its june itll get data from may
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
$end_date = $current_year . '-' . str_pad($current_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00';*/

$charges = query_ezpass("SELECT * FROM supercharger WHERE (date_and_time >= ? AND date_and_time < ?) AND match_status = ? ORDER BY date_and_time DESC", $start_date, $end_date, "not matched");

//echo "<pre>";
//print_r($charges);
//echo "</pre>";
// Rename 'date_and_time' to 'transaction_format_date' in the $charges array
foreach ($charges as $key => $charge) {
    $charges[$key]['transaction_format_date'] = $charge['date_and_time'];
    unset($charges[$key]['date_and_time']);
    $vin_key = array_search($charge['vin'], array_column($cars, 'vin'));
    $charges[$key]['car_nickname'] = $cars[$vin_key]['car_nickname'];

    $charges[$key]['location_abbr'] = formatAddress($charge['location']);
    $charges[$key]['type'] = "supercharger"; 
    $charges[$key]['row_id'] = $charge['id'];
}



$results = []; // Initialize an empty array to store the row data
foreach ($cars as $car) {
    $total = 0;
    $plate = $car['plate'];
    $tag = $car['ezpass'];
    $vin = $car['vin'];
    

    $rows = query_ezpass("SELECT * FROM ezpass WHERE (transaction_format_date >= ? AND transaction_format_date < ?) AND (tag_plate = ? || tag_plate = ?) AND (match_status = ? OR match_status = ?) ORDER BY transaction_format_date DESC", $start_date, $end_date, $plate, $tag, "not matched", "not tested");

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

   // echo "<br/>";
   // echo $car['car_nickname'] . "<br/>";
   // echo "total: " . $total;
   // echo "<br/>";
    //echo "<br/>";
   // echo "<br/>";
}

$combinedArray = array_merge($results, $charges);



foreach ($combinedArray as $key => $value) {
    $carName = $value['car_nickname'];
    $transactionDate = $value['transaction_format_date'];
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $transactionDate);

    // Subtract 7 days
    $date->modify('-7 days');

    // Get the new date in the same format as the input
    $beforeDate = $date->format('Y-m-d H:i:s');

     // Subtract 7 days
    $date->modify('+14 days');
    $afterDate = $date->format('Y-m-d H:i:s');

    $prevTripsHQ = query("SELECT * FROM reservations WHERE return_date >= ? AND return_date <= ? AND vehicle_key = ? ORDER BY return_date DESC LIMIT 1", $beforeDate, $transactionDate, $value['car_nickname']);

    foreach ($prevTripsHQ as $key1 => $HQTrip) {
        $prevTripsHQ[$key1]['channel'] = "HQ";
        //unset($prevTripsTuro[$key]['return_datetime']);
    }

    $prevTripsTuro = query("SELECT * FROM turo_reservations WHERE return_datetime >= ? AND return_datetime <= ? AND vehicle_key = ? ORDER BY return_datetime DESC LIMIT 1", $beforeDate, $transactionDate, $value['car_nickname']);

    foreach ($prevTripsTuro as $key2 => $turoTrip) {
        $prevTripsTuro[$key2]['return_date'] = $turoTrip['return_datetime'];
        $prevTripsTuro[$key2]['channel'] = "Turo";
        //unset($prevTripsTuro[$key]['return_datetime']);
    }

    $prevTrips = array_merge($prevTripsHQ, $prevTripsTuro);

    usort($prevTrips, function ($a, $b) {
        return strtotime($b['return_date']) - strtotime($a['return_date']);
    });
    

    $value['prev_trips'] = $prevTrips;
    $combinedArray[$key] = $value;

    $afterTripsHQ = query("SELECT * FROM reservations WHERE pickup_date >= ? AND pickup_date <= ? AND vehicle_key = ? ORDER BY pickup_date ASC LIMIT 1", $transactionDate, $afterDate, $value['car_nickname']);

    foreach ($afterTripsHQ as $key3 => $HQTrip) {
        $afterTripsHQ[$key3]['channel'] = "HQ";
        //unset($prevTripsTuro[$key]['pickup_datetime']);
    }
    $afterTripsTuro = query("SELECT * FROM turo_reservations WHERE pickup_datetime >= ? AND pickup_datetime <= ? AND vehicle_key = ? ORDER BY pickup_datetime ASC LIMIT 1", $transactionDate, $afterDate, $value['car_nickname']);

    foreach ($afterTripsTuro as $key4 => $turoTrip) {
        $afterTripsTuro[$key4]['pickup_date'] = $turoTrip['pickup_datetime'];
        $afterTripsTuro[$key4]['channel'] = "Turo";
        //unset($prevTripsTuro[$key]['pickup_datetime']);
    }

    $afterTrips = array_merge($afterTripsHQ, $afterTripsTuro);
    usort($afterTrips, function ($a, $b) {
        return strtotime($a['pickup_date']) - strtotime($b['pickup_date']);
    });

    $value['after_trips'] = $afterTrips;
    $combinedArray[$key] = $value;
    
}
usort($combinedArray, function ($a, $b) {
    // Compare VINs
    $vinComparison = strcmp($a['vin'], $b['vin']);
    if ($vinComparison != 0) {
        return $vinComparison;
    }

    // If VINs are the same, compare dates
    return strtotime($b['transaction_format_date']) - strtotime($a['transaction_format_date']);
});
$array = $combinedArray;
// Pretty print the combined and sorted array
if (!empty($no_match_results)) {
     echo "Alert Mordy that a charge/s were found that dont match to any ezpass or toll transponder.";
    echo "<pre>";
    print_r($no_match_results);
    echo "</pre>";
}

?>
