<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$postdata = http_build_query(
    array(
        'vehicle_id' => '7',
        'charge_date' => '2023-03-24 2:00AM',
        'charge_amount' => '10',
        'label' => 'testone'
    )
);
$opts = array(
  'http'=>array(
    'method'=>"POST",
    'header'=>"Accept-language: en\r\n" .
              "Authorization: Basic d0hJS3c0dm9ENVFvcXIyRXQ5ZkxQak9lYUJ4QmZHY3o5Zkp2N3RqbDFpU0c0dXJHOGs6RDl6aktBRjN5MHpGV09jd0VuNXRrSVBYRzZiZjU1SHdlbWNldmNsdkNZMlB3RVJIZWU=\r\n",
    'content' => $postdata
  )
);

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents('https://api-america-3.us5.hqrentals.app/api-america-3/car-rental/reservations/74/external-charges', false, $context);
$x = json_decode($file, true);


?>
<pre><?php print_r($x) ?></pre>