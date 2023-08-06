<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'functions.php';

/*$opts = array(
    'http'=>array(
      'method'=>"DELETE",
      'header'=>"Accept-language: en\r\n" .
                "Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=\r\n"
    )
  );*/
  $opts = array(
    'http'=>array(
      'method'=>"GET",
      'header'=>"Accept-language: en\r\n" .
                "Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=\r\n"
    )
  );


  $context = stream_context_create($opts);


//THIS ONLY GETS UP TO 50 RESULTS. MODIFY TO INCLUDE ALL RESERVATIONS 
// Open the file using the HTTP headers set above


  //for ($i=1; $i < 100000; $i++) {
      $file = file_get_contents('https://api-america-3.us5.hqrentals.app/api-america-3/car-rental/reservations/' . "8/adjustments", false, $context);
     
        $x = json_decode($file, true);
        $x = $x['data'];

        //echo "count" . count($x) . "<br/>";
        echo $x['reservation']['id'] . "<br/>";
        echo $x['customer']['last_name'] . "<br/>";
        echo $vehicle = explode(" (", $x['vehicles']['0']['vehicle']['label'])[0];
        echo "<br/>";
        echo $x['reservation']['pick_up_date'] . "<br/>";
        echo $x['reservation']['return_date'] . "<br/>";
        echo $x['reservation']['status'] . "<br/>";
        echo "<br/>";
        //query("INSERT INTO reservations (hq_id, last_name, vehicle_key, pickup_date, return_date, status) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE last_name = ?, vehicle_key = ?, pickup_date = ?, return_date = ?, status = ?", $x['reservation']['id'], $x['customer']['last_name'], $vehicle, $x['reservation']['pick_up_date'], $x['reservation']['return_date'], $x['reservation']['status'], $x['customer']['last_name'], $vehicle, $x['reservation']['pick_up_date'], $x['reservation']['return_date'], $x['reservation']['status']);

  //}


?>
<pre><?php print_r($x) ?></pre>