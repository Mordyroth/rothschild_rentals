<?php
if (isset($_POST['id']) && isset($_POST['action'])) {
  $carId = $_POST['id'];
  $action = $_POST['action'];
  
  $curl = curl_init();
  $url = "https://api.tessie.com/" . $carId . "/command/" . $action . "?retry_duration=40&wait_for_completion=true";
  
  curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
      "accept: application/json",
      "authorization: Bearer KGFg3LgJLXIHwHLDdyxb3oJk8e4woOCX"
    ],
  ]);

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);



  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    echo $response;
  }
}
?>
