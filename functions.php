<?php
date_default_timezone_set('America/New_York');
// your database's name
define("DATABASE", "rothschild_rentals");

// your database's password
define("PASSWORD", "mordyroth0430");

// your database's server
define("SERVER", "localhost");

// your database's username
define("USERNAME", "root");

//url of server
    define("HOST_URL", "http://52.42.72.117/");

   

function query(/* $sql [, ... ] */)
{
    // SQL statement
    $sql = func_get_arg(0);

    // parameters, if any
    $parameters = array_slice(func_get_args(), 1);

    // try to connect to database
    static $handle;
    if (!isset($handle))
    {
        try
        {
            // connect to database
            $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

            // ensure that PDO::prepare returns false when passed invalid SQL
            $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
        }
        catch (Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            exit;
        }
    }

    // prepare SQL statement
    $statement = $handle->prepare($sql);
    if ($statement === false)
    {
        trigger_error($handle->errorInfo()[2], E_USER_ERROR);
        exit;
    }

    // execute SQL statement
    $results = $statement->execute($parameters);
    
    if ($results === false)
    {
        return $results;
        //trigger_error($statement->errorInfo()[2], E_USER_ERROR);
        exit;
    }
    // return result set's rows
   // return $results;
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function query_ezpass(/* $sql [, ... ] */)
{
    // SQL statement
    $sql = func_get_arg(0);

    // parameters, if any
    $parameters = array_slice(func_get_args(), 1);

    // try to connect to database
    static $handle;
    if (!isset($handle))
    {
        try
        {
            // connect to database
            $handle = new PDO("mysql:dbname=" . "tolls" . ";host=" . SERVER, USERNAME, PASSWORD);

            // ensure that PDO::prepare returns false when passed invalid SQL
            $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
        }
        catch (Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            exit;
        }
    }

    // prepare SQL statement
    $statement = $handle->prepare($sql);
    if ($statement === false)
    {
        trigger_error($handle->errorInfo()[2], E_USER_ERROR);
        exit;
    }

    // execute SQL statement
    $results = $statement->execute($parameters);
    
    if ($results === false)
    {
        return $results;
        //trigger_error($statement->errorInfo()[2], E_USER_ERROR);
        exit;
    }
    // return result set's rows
   // return $results;
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}


function find_vehicle_info($search) {
    $vehicle_info = query_ezpass("SELECT * FROM car_info");
    foreach ($vehicle_info as $item) {
        if ($item['plate'] == $search || $item['ezpass'] == $search) {
            return $item;
        }
    }
    return null;
}

function upload_hq_reservations() {
  $opts = array(
    'http'=>array(
      'method'=>"GET",
      'header'=>"Accept-language: en\r\n" .
                "Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=\r\n"
    )
  );

  $context = stream_context_create($opts);


// Open the file using the HTTP headers set above

  $error = 0;
  for ($i=1; $i < 100000; $i++) {
      $file = file_get_contents('https://api-america-3.us5.hqrentals.app/api-america-3/car-rental/reservations/' . $i, false, $context);
      if (!$file) {
        echo "empty";
        $error++;
        if ($error > 10) {
          break;
        } else {
          continue;
        }
      } else {
        $error = 0;
        echo "full";
        $x = json_decode($file, true);
        $x = $x['data'];

        //echo "count" . count($x) . "<br/>";
        echo $x['reservation']['id'] . "<br/>";
        echo $x['customer']['last_name'] . "<br/>";
        echo $vehicle = explode(" (", $x['vehicles']['0']['vehicle']['label'])[0];
        $vehicles = "";
        if (count($x['vehicles']) > 1) {
            $vehicles = stringifyVehicleLabels($x['vehicles']);
        }
        echo "<br/>";
        echo $x['reservation']['pick_up_date'] . "<br/>";
        echo $x['reservation']['return_date'] . "<br/>";
        echo $x['reservation']['status'] . "<br/>";
        echo "<br/>";
         $vehicle_id = $x['vehicles'][0]['vehicle']['id'];
      $label = $x['vehicles'][0]['vehicle']['label'];
      $plate = $x['vehicles'][0]['vehicle']['plate'];
      $total_days = $x['selected_vehicle_class']['price']['total_days'];
      $rack_price = $x['reservation']['rack_price'];
      $discounts_amount = $x['reservation']['discounts_amount'];
      $external_charges_price = $x['reservation']['external_charges_price'];
      $total_price = $x['reservation']['total_price']['usd_amount'];
      $notes = $x['reservation']['notes'];

      query("INSERT INTO reservations (hq_id, last_name, vehicle_key, pickup_date, return_date, status, multiple_vehicles, vehicle_id, label, plate, total_days, rack_price, discounts_amount, external_charges_price, total_price, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE last_name = ?, vehicle_key = ?, pickup_date = ?, return_date = ?, status = ?, multiple_vehicles = ?, vehicle_id = ?, label = ?, plate = ?, total_days = ?, rack_price = ?, discounts_amount = ?, external_charges_price = ?, total_price = ?, notes = ?", $x['reservation']['id'], $x['customer']['last_name'], $vehicle, $x['reservation']['pick_up_date'], $x['reservation']['return_date'], $x['reservation']['status'], $vehicles, $vehicle_id, $label, $plate, $total_days, $rack_price, $discounts_amount, $external_charges_price, $total_price, $notes, $x['customer']['last_name'], $vehicle, $x['reservation']['pick_up_date'], $x['reservation']['return_date'], $x['reservation']['status'], $vehicles, $vehicle_id, $label, $plate, $total_days, $rack_price, $discounts_amount, $external_charges_price, $total_price, $notes);
        // get total ezpass and supercharger fees


        //query("INSERT INTO reservations (hq_id, last_name, vehicle_key, pickup_date, return_date, status, multiple_vehicles) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE last_name = ?, vehicle_key = ?, pickup_date = ?, return_date = ?, status = ?, multiple_vehicles = ?", $x['reservation']['id'], $x['customer']['last_name'], $vehicle, $x['reservation']['pick_up_date'], $x['reservation']['return_date'], $x['reservation']['status'], $vehicles, $x['customer']['last_name'], $vehicle, $x['reservation']['pick_up_date'], $x['reservation']['return_date'], $x['reservation']['status'], $vehicles);

      }
  }
}

function stringifyVehicleLabels($inputArray) {
    $outputArray = [];

    foreach ($inputArray as $key => $vehicleData) {
        $outputArray[$key] = [
            'label' => $vehicleData['vehicle']['label'],
            'pick_up_date' => $vehicleData['pick_up_date'],
            'return_date' => $vehicleData['return_date'],
        ];
    }

    return json_encode($outputArray);
}

function car_info($nickname) {
    $info = query_ezpass("SELECT * FROM car_info WHERE car_nickname = ?", $nickname);
    if (empty($info)) {
        $info = query_ezpass("SELECT * FROM car_info WHERE car_nickname_1 = ?", $nickname);
    }
    return $info[0];
}

function match_up_ezpass() {
    $tolls = query_ezpass("SELECT * FROM ezpass WHERE (match_status IS NULL OR match_status = ? OR match_status = ?)  AND (match_status != ?)", "not matched", "not tested", "Service Fee");

    $trips = query("SELECT * FROM reservations");

    // Loop through each toll charge and assign it to the corresponding customer
    foreach ($tolls as $toll) {

        // Find the customer that rented the car with the same plate number as the toll charge
        // and whose rental period includes the toll transaction date
        $customer = null;
        foreach ($trips as $trip) {
            //print_r($trip);
            // Extract the date and time parts from the transaction_date string
            //$dateTimeParts = explode(' at ', $toll['transaction_date']);
            $date = $toll['transaction_date'];
            $time = $toll['exit_time'];

            // Create a DateTime object from the date and time parts
            $dateTime = DateTime::createFromFormat('m/d/Y H:i:s', "$date $time");

            // Get the date string in the format 'Y-m-d H:i:s'
            $formattedDate = $dateTime->format('Y-m-d H:i:s');

            // Use strtotime() to parse the formatted date string
            $timestamp = strtotime($formattedDate);

            // If the timestamp is in the future, subtract one year from the dateTime object
           /* if ($timestamp > time()) {
                $dateTime->sub(new DateInterval('P1Y'));
                $formattedDate = $dateTime->format('Y-m-d H:i:s');
                $timestamp = strtotime($formattedDate);
            }*/

            // If the plate number and rental period match, add the toll charge to the customer array
             if (!empty($trip['vehicle_key'])) {
                $car_info = car_info($trip['vehicle_key']);
            } else {
                //echo "No Vehicle Key Ezpass matchup, res #" . $trip['hq_id'] . "<br/>";
                continue;
            }
            

            if (($toll['tag_plate'] == $car_info['plate'] || $toll['tag_plate'] == $car_info['ezpass']) &&
                strtotime($trip['pickup_date']) <= $timestamp &&
                strtotime($trip['return_date']) >= $timestamp) {
                query_ezpass("UPDATE ezpass SET match_status = ?, matched = ? WHERE id = ?", "matched", "hq_" . $trip['hq_id'], $toll['id']);
                break;
            } else {
                query_ezpass("UPDATE ezpass SET match_status = ? WHERE id = ?", "not matched", $toll['id']);
            }
        }
    }
}

function delete_duplicate_ezpass() {
    $x = query_ezpass("CREATE TEMPORARY TABLE temp_table AS (SELECT id FROM ezpass WHERE id NOT IN (SELECT MIN(id) FROM ezpass GROUP BY transaction_date, exit_time, amount, tag_plate))");
    $k = query_ezpass("SELECT * FROM ezpass WHERE id IN (SELECT id FROM temp_table)");
    $y = query_ezpass("DELETE FROM ezpass WHERE id IN (SELECT id FROM temp_table)");
}

function match_up_superchargers() {
    $charges = query_ezpass("SELECT * FROM supercharger WHERE match_status != ?", "matched");
    echo count($charges) . "<br/>";

    $trips = query("SELECT * FROM reservations");

    // Loop through each toll charge and assign it to the corresponding customer
    foreach ($charges as $charge) {
        // Find the customer that rented the car with the same plate number as the toll charge
        // and whose rental period includes the toll transaction date
        foreach ($trips as $trip) {
            $dateTime = new DateTime($charge['date_and_time']);

            // Get the date string in the format 'Y-m-d H:i:s'
            $formattedDate = $dateTime->format('Y-m-d H:i:s');

            // Use strtotime() to parse the formatted date string
            $timestamp = strtotime($formattedDate);


            // If the plate number and rental period match, add the toll charge to the customer array
            if (!empty($trip['vehicle_key'])) {
                $car_info = car_info($trip['vehicle_key']);
            } else {
                echo "No Vehicle Key Supercharger matchup, res #" . $trip['hq_id'] . "<br/>";
                continue;
            }
            

            if (($charge['vin'] == $car_info['vin']) && strtotime($trip['pickup_date']) <= $timestamp && strtotime($trip['return_date']) >= $timestamp) {
                echo "matched";
                query_ezpass("UPDATE supercharger SET match_status = ?, matched = ? WHERE id = ?", "matched", "hq_" . $trip['hq_id'], $charge['id']);
               break;
            } else {
                query_ezpass("UPDATE supercharger SET match_status = ? WHERE id = ?", "not matched", $charge['id']);
            }
        }
    }
}

function match_up_superchargers_turo() {
    $charges = query_ezpass("SELECT * FROM supercharger WHERE match_status != ?", "matched");
    echo count($charges) . "<br/>";

    $trips = query("SELECT * FROM turo_reservations");

    // Loop through each toll charge and assign it to the corresponding customer
    foreach ($charges as $charge) {
        // Find the customer that rented the car with the same plate number as the toll charge
        // and whose rental period includes the toll transaction date
        foreach ($trips as $trip) {
            //print_r($trip);
            // Extract the date and time parts from the transaction_date string
            //$dateTimeParts = explode(' at ', $toll['transaction_date']);

            // Create a DateTime object from the date and time parts
            //$dateTime = DateTime::createFromFormat('m/d/Y H:i:s', "$date $time");
            
            $dateTime = new DateTime($charge['date_and_time']);

            // Get the date string in the format 'Y-m-d H:i:s'
            $formattedDate = $dateTime->format('Y-m-d H:i:s');

            // Use strtotime() to parse the formatted date string
            $timestamp = strtotime($formattedDate);


            // If the plate number and rental period match, add the toll charge to the customer array
            if (!empty($trip['vehicle_key'])) {
                $car_info = car_info($trip['vehicle_key']);
            } else {
                echo "No Vehicle Key Supercharger matchup, res #" . $trip['hq_id'] . "<br/>";
                continue;
            }
            

            if (($charge['vin'] == $car_info['vin']) && strtotime($trip['pickup_datetime']) <= $timestamp && strtotime($trip['return_datetime']) >= $timestamp) {
                echo "matched";
                query_ezpass("UPDATE supercharger SET match_status = ?, matched = ? WHERE id = ?", "matched", "Turo_" . $trip['turo_id'], $charge['id']);
               break;
            } else {
                query_ezpass("UPDATE supercharger SET match_status = ? WHERE id = ?", "not matched", $charge['id']);
            }
        }
    }
}

function car_info_vin($vin) {
    return $info = query_ezpass("SELECT * FROM car_info WHERE vin = ?", $vin)[0];
}

function findCharge($array, $amount, $chargeDate) {
    date_default_timezone_set('America/New_York');
    $chargeDate = date('Y-m-d H:i:s', strtotime($chargeDate)); // convert chargedate value to 'Y-m-d H:i:s' format
    foreach ($array as $element) {
        if ($element['charge_amount']['amount'] == $amount && $element['charge_date'] == $chargeDate) {
            return $element;
        }
    }
    return null; // return null if no matching element is found
}

function post_supercharger_charges() {
    $charges = query_ezpass("SELECT * FROM supercharger WHERE matched IS NOT NULL AND matched LIKE ? AND hq_charge_id IS NULL", "hq%");
    foreach ($charges as $charge) {
        //reservation id
        $reservation = explode("_", $charge['matched'])[1];
        $url = 'https://api-america-3.us5.hqrentals.app/api-america-3/car-rental/reservations/' . $reservation . '/external-charges';

        //get car id
        $vehicle_id = car_info_vin($charge['vin'])['hq_vehicle_id'];

        $data = array(
        'vehicle_id' => $vehicle_id,
        'charge_date' => $charge['date_and_time'],
        'charge_amount' => $charge['amount'],
        'label' => "Supercharger " . $charge['location']
        // add more parameters as needed
        );
        // Encode the request parameters as JSON
        $data_json = json_encode($data);

        // Set the HTTP headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=',
        );

        // Set the context options for the HTTP request
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $data_json,
            ),
        );

        // Create a stream context for the HTTP request
        $context = stream_context_create($options);

        // Send the HTTP request and get the response
        $response = file_get_contents($url, false, $context);

        // Process the response data
        $response_data = json_decode($response, true);
        if ($response_data['success']) {
            $postedCharge = findCharge($response_data['data']['external_charges'], $charge['amount'], $charge['date_and_time'])['id'];

            query_ezpass("UPDATE supercharger SET hq_charge_id = ? WHERE id = ?", $postedCharge, $charge['id']);
        }
    }
}

function findChargeEzpass($array, $amount, $chargeDate) {
    date_default_timezone_set('America/New_York');
    $chargeDate = date('Y-m-d H:i:s', strtotime($chargeDate)); // convert chargedate value to 'Y-m-d H:i:s' format
    foreach ($array as $element) {
        if ($element['charge_amount']['amount'] == $amount && $element['charge_date'] == $chargeDate) {
            return $element;
        }
    }
    return null; // return null if no matching element is found
}

function post_ezpass_charges() {
    $tolls = query_ezpass("SELECT * FROM ezpass WHERE matched IS NOT NULL AND matched LIKE ? AND hq_charge_id IS NULL", "hq%");

    foreach ($tolls as $toll) {
        //reservation id
        $reservation = explode("_", $toll['matched'])[1];
        $url = 'https://api-america-3.us5.hqrentals.app/api-america-3/car-rental/reservations/' . $reservation . '/external-charges';

        //get car id
        $vehicle_id = find_vehicle_info($toll['tag_plate'])['hq_vehicle_id'];
        $data = array(
        'vehicle_id' => $vehicle_id,
        'charge_date' => $toll['transaction_date'] . " " . $toll['exit_time'],
        'charge_amount' => preg_replace("/[^0-9\.]/", "", $toll['amount']),
        'label' => $toll['agency'] . ", " . $toll["exit_plaza"]
        // add more parameters as needed
        );
        // Encode the request parameters as JSON
        $data_json = json_encode($data);

        // Set the HTTP headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=',
        );

        // Set the context options for the HTTP request
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $data_json,
            ),
        );

        // Create a stream context for the HTTP request
        $context = stream_context_create($options);

        // Send the HTTP request and get the response
        $response = file_get_contents($url, false, $context);

        // Process the response data
        $response_data = json_decode($response, true);
        if ($response_data['success']) {
            $charge = findChargeEzpass($response_data['data']['external_charges'], preg_replace("/[^0-9\.]/", "", $toll['amount']), $toll['transaction_date'] . " " . $toll['exit_time'])['id'];

            query_ezpass("UPDATE ezpass SET hq_charge_id = ? WHERE id = ?", $charge, $toll['id']);
            query("UPDATE reservations SET transponder_charge_pending = ? WHERE hq_id = ?", "1", $reservation);

        }
    }
    //return $response_data;
}

function isSimilarCharge($date1, $date2) {
    $dt1 = new DateTime($date1);
    $dt2 = new DateTime($date2);
    $interval = $dt1->diff($dt2);
    $minutes = $interval->days * 24 * 60;
    $minutes += $interval->h * 60;
    $minutes += $interval->i;
    echo "<br/> dt1: " . $date1 . "<br/>";
    echo "dt2: " . $date2 . "<br/>";

    $interval = $dt1->diff($dt2);

    if ($minutes <= 70) {
        echo $minutes . "  true <br/><br/>"; 
        return true;
    } else {
        echo $minutes . "  false <br/><br/>"; 
        return false;
    }
}

function import_supercharger() {
    $teslas = query_ezpass("SELECT * FROM car_info WHERE tessie_auth IS NOT NULL");

    foreach ($teslas as $tesla) {
        $vin = $tesla['vin'];
        $auth = $tesla['tessie_auth'];

        $curl = curl_init();


        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.tessie.com/" . $vin . "/charges?distance_format=mi&format=json&superchargers_only=true&exclude_origin=false&timezone=UTC",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer " . $auth
          ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
            $results = json_decode($response, true)['results'];
            foreach ($results as $result) {
                if (empty($result['cost']) || $result['cost'] == "0.00") {
                    continue;
                }
                $timestamp = $result['started_at'];
                echo "started: " . $timestamp . "<br/>";
                echo "location: " . $result['location'] . "<br/>";
                echo "amount: " . $result['cost'] . "<br/>";
                $timezone = new DateTimeZone('America/New_York');
                $date = new DateTime("@$timestamp"); // Create a new DateTime object with the Unix timestamp
                $date->setTimezone($timezone);
                $formatted_date = $date->format('Y-m-d H:i:s'); // Format the date
                echo $formatted_date . "<br/>"; // Output: 2021-01-24T17:31:55-05:00

                // check if this input already exsists
                $charges = query_ezpass("SELECT * FROM supercharger WHERE vin = ? AND (amount IS NULL OR amount = ?)", $vin, $result['cost']);
                $alreadyExsists = false;
                if ($charges) {
                    foreach ($charges as $charge) {
                        // check if charge is within two minutes
                        if ($charge['invoice'] == $result['id'] || isSimilarCharge($charge['date_and_time'], $formatted_date)) {
                            echo "alreadyExsists <br/>";
                            $alreadyExsists = true;
                            break;
                        }
                    }
                }
                if ($alreadyExsists) {
                    continue;
                } else {
                    query_ezpass("INSERT INTO supercharger (date_and_time, vin, location, invoice, amount) VALUES (?,?,?,?,?)", $formatted_date, $vin, $result['location'], $result['id'], $result['cost']);
                }
            }
        }
    }
}


function transponderRentalDays($hq_id) {
    $reservation = query("SELECT * FROM reservations WHERE hq_id = ?", $hq_id);
    $pickupDate = $reservation[0]['pickup_date'];
    $returnDate = $reservation[0]['return_date'];
    $days = getDaysBetweenDatesExcludingSaturdays($pickupDate, $returnDate);


    $charges = query_ezpass("SELECT * FROM ezpass WHERE matched = ?", "hq_" . $hq_id);
    if (empty($charges)) {
        echo "empty";
        return false;
    }
    foreach ($charges as $charge) {
        $datesArray[] = $charge['transaction_date'];
    }
    if ($datesArray) {
        $count = countUniqueDates($datesArray); // Returns 3, because there are 3 unique dates in the array
        if ($count > $days) {
            $count = $days;
        }
    }
    return $count;
}

function countUniqueDates($datesArray) {
    $uniqueDates = array(); // Initialize an empty array to store unique dates

    // Loop through each date in the array
    foreach ($datesArray as $date) {
        $dateObj = DateTime::createFromFormat('d/m/Y', $date); // Convert date string to DateTime object
        $dateStr = $dateObj->format('Y-m-d'); // Convert DateTime object to string in 'yyyy-mm-dd' format

        if (!in_array($dateStr, $uniqueDates)) { // Check if the date is already in the uniqueDates array
            $uniqueDates[] = $dateStr; // If not, add it to the array
        }
    }
    echo "<br/> unique Days: " . count($uniqueDates) . "<br/>";
    return count($uniqueDates); // Return the count of unique dates
}

function getDaysBetweenDatesExcludingSaturdays($pickupDate, $returnDate) {
    $pickupDateTime = new DateTime($pickupDate); // Convert pickup date string to DateTime object
    $returnDateTime = new DateTime($returnDate); // Convert return date string to DateTime object

    // Check if the return date is before the pickup date
    if ($returnDateTime < $pickupDateTime) {
        return 0; // Return 0 if the return date is before the pickup date
    }

    // Calculate the interval between the two dates, including the time
    $interval = $returnDateTime->diff($pickupDateTime);
    print_r($interval);
    $days = $interval->days;
    if ($interval->h) {
        $days++;
    }
    $num_saturdays = 0;

    // loop through each day between the pickup and return dates
    while ($pickupDateTime <= $returnDateTime) {
        // if the current day is a Saturday, increment the counter
        if ($pickupDateTime->format('N') == 6) { // 6 = Saturday
            $num_saturdays++;
        }
        // move to the next day
        $pickupDateTime->modify('+1 day');
    }

    $days = $days - $num_saturdays;

    return max($days, 0); // Return the number of days between the two dates, excluding Saturdays
}


function chargeTransponderRental() {
    $opts = array(
        'http'=>array(
          'method'=>"GET",
          'header'=>"Accept-language: en\r\n" .
                    "Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=\r\n"
        )
      );

    $context = stream_context_create($opts);

    $reservations = query("SELECT * FROM reservations WHERE transponder_charge_pending = ?", "1");
    if ($reservations) {
        foreach ($reservations as $reservation) {
            unset($additionalChargesArray);
            $hq_id = $reservation['hq_id'];
            $file = file_get_contents('https://api-america-3.us5.hqrentals.app/api-america-3/car-rental/reservations/' . $hq_id, false, $context);
            $x = json_decode($file, true);
            $x = $x['data'];

            echo $x['reservation']['id'] . "<br/>";
            echo $x['customer']['last_name'] . "<br/>";
            echo $vehicle = explode(" (", $x['vehicles']['0']['vehicle']['label'])[0];
            echo "<br/>";
            echo $x['reservation']['pick_up_date'] . "<br/>";
            echo $x['reservation']['return_date'] . "<br/>";
            echo $x['reservation']['status'] . "<br/>";
            echo "<br/>";
            $ezpassDays = transponderRentalDays($hq_id);
            if (empty($ezpassDays)) {
                query("UPDATE reservations SET transponder_charge_pending = ? WHERE hq_id = ?", "0", $hq_id);
                echo "<br/>no ezpass used: " . $hq_id . "<br/>";
                continue;
            }
            echo "<br/> expass days: " . $ezpassDays . "<br/>";
            // create array for additional charges
            $additionalChargesArray[] = "12_" . $ezpassDays;

            // get all current additional charges
            foreach ($x['selected_additional_charges'] as $additionalCharge) {
                if ($additionalCharge['id'] == 12) {
                    continue;
                }
                $additionalChargesArray[] = $additionalCharge['id'] . "_" . $additionalCharge['selected_quantity'];
            }

            $data = array('additional_charges' => $additionalChargesArray);
            // Encode the request parameters as JSON
            $data_json = json_encode($data);

            // Set the HTTP headers
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=',
            );

            // Set the context options for the HTTP request
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => implode("\r\n", $headers),
                    'content' => $data_json,
                ),
            );

            // Create a stream context for the HTTP request
            $context1 = stream_context_create($options);
            $url = 'https://api-america-3.us5.hqrentals.app/api-america-3/car-rental/reservations/' . $hq_id . '/update';
            // Send the HTTP request and get the response
            $response = file_get_contents($url, false, $context1);

            // Process the response data
            $response_data = json_decode($response, true);
            if ($response_data['success']) {
                echo "succsess";
                query("UPDATE reservations SET transponder_charge_pending = ? WHERE hq_id = ?", "0", $hq_id);
            }
        }
    }
}

function match_up_ezpass_turo() {
    $tolls = query_ezpass("SELECT * FROM ezpass WHERE (match_status IS NULL OR match_status = ? OR match_status = ?)  AND (match_status != ?)", "not matched", "not tested", "Service Fee");
    //echo count($tolls) . "<br/>";

    $trips = query("SELECT * FROM turo_reservations");
        ?><pre><?php print_r($trips) ?></pre><?php
    // Loop through each toll charge and assign it to the corresponding customer
    foreach ($tolls as $toll) {
        // Find the customer that rented the car with the same plate number as the toll charge
        // and whose rental period includes the toll transaction date
        $customer = null;
        foreach ($trips as $trip) {
            //print_r($trip);
            // Extract the date and time parts from the transaction_date string
            //$dateTimeParts = explode(' at ', $toll['transaction_date']);
            $date = $toll['transaction_date'];
            $time = $toll['exit_time'];

            // Create a DateTime object from the date and time parts
            $dateTime = DateTime::createFromFormat('m/d/Y H:i:s', "$date $time");

            // Get the date string in the format 'Y-m-d H:i:s'
            $formattedDate = $dateTime->format('Y-m-d H:i:s');

            // Use strtotime() to parse the formatted date string
            $timestamp = strtotime($formattedDate);


            // If the plate number and rental period match, add the toll charge to the customer array
             if (!empty($trip['vehicle_key'])) {
                $car_info = car_info($trip['vehicle_key']);
            } else {
                //echo "No Vehicle Key Ezpass matchup, res #" . $trip['hq_id'] . "<br/>";
                continue;
            }
            

            if (($toll['tag_plate'] == $car_info['plate'] || $toll['tag_plate'] == $car_info['ezpass']) &&
                strtotime($trip['pickup_date']) <= $timestamp &&
                strtotime($trip['return_date']) >= $timestamp && preg_replace('/[^\d.]/', '', $toll['amount']) < 20) {
                query_ezpass("UPDATE ezpass SET match_status = ?, matched = ? WHERE id = ?", "matched", "turo_" . $trip['turo_id'], $toll['id']);
                break;
            } else {
                if ($toll['description'] == "Service Fee") {
                    $status = "Service Fee";
                } else {
                    $status = "not matched";
                }
                query_ezpass("UPDATE ezpass SET match_status = ? WHERE id = ?", $status, $toll['id']);
            }
        }
    }
}

function convertDateTime($date, $time) {
    // Combine the date and time into a single string
    $input = $date . ' ' . $time;
    // Define the input format
    $input_format = "m/d/Y H:i:s";

    // Create a DateTime object from the input string
    $datetime = DateTime::createFromFormat($input_format, $input);

    if ($datetime === false) {
        return "Error parsing the date and time!";
    } else {
        // Define the output format
        $output_format = "Y-m-d H:i:s";
        // Convert the DateTime object to the desired format
        $output = $datetime->format($output_format);

        return $output;
    }
}

function add_ezpass_and_supercharger() {
    $x = query_ezpass("SELECT matched, SUM(amount) AS total_amount FROM supercharger WHERE match_status = ? GROUP BY matched", "matched");

    foreach ($x as $customer) {
        $platform = explode("_", $customer['matched']);
        if ($platform[0] == "hq") {
            query("UPDATE reservations SET total_supercharger = ? WHERE hq_id = ?", $customer['total_amount'], $platform[1]);    
        }
    }

    //$rows = query_ezpass("SELECT matched, SUM(CAST(REPLACE(REPLACE(amount, '($', '-'), ')', '') AS DECIMAL(10, 2))) AS total_amount FROM ezpass WHERE match_status = ? GROUP BY matched", "matched");
    $rows = query_ezpass("SELECT matched, SUM(CAST(clean_amount AS DECIMAL(10, 2))) AS total_amount FROM (SELECT matched, REPLACE(REPLACE(REPLACE(amount, '($', ''), ')', ''), '$', '') AS clean_amount FROM ezpass WHERE match_status = ?) as derivedTable GROUP BY matched", "matched");


    $totalAmounts = array();
    foreach ($rows as $row) {
        $amount = preg_replace("/[^0-9.]/", "", $row['total_amount']);
        $totalAmounts[$row['matched']] = floatval($amount);
    }
    foreach ($totalAmounts as $matched => $totalAmount) {
        $platform = explode("_", $matched);
        //echo "Customer: $matched, Total Amount: $totalAmount" . PHP_EOL;
        if ($platform[0] == "hq") {
            query("UPDATE reservations SET total_tolls = ? WHERE hq_id = ?", $totalAmount, $platform[1]);    
        }
    }
    return $totalAmounts;
}
?>