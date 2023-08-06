<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'functions.php';
if (isset($_POST['action']) && $_POST['action'] == 'get_cars') {
  // return list of cars as JSON
	$cars = query_ezpass("SELECT car_nickname FROM car_info");
	$nicknamesArray = array_column($cars, 'car_nickname');
  //$cars = array('Toyota', 'Honda', 'Ford', 'Chevrolet');
  echo json_encode($nicknamesArray);
  exit;
}

if (isset($_POST['car']) && isset($_POST['amount']) && isset($_POST['datetime'])) {
  // handle form submission
  $car = $_POST['car'];
  $amount = floatval($_POST['amount']);
  $type = isset($_POST['type']) ? ($_POST['type'] == 'income' ? 'income' : 'expense') : '';
  
  // validate date and time
  $datetime = strtotime($_POST['datetime']);
  if ($datetime === false) {
    echo "ERROR WITH DATE!";
    exit;
  }
  $datetime = date('Y-m-d H:i:s', $datetime);

  $input_by = isset($_POST['input-by']) ? $_POST['input-by'] : '';
  
  // optional fields
  $paid = isset($_POST['paid']) ? $_POST['paid'] : '';
  $comments = isset($_POST['comments']) ? $_POST['comments'] : '';
  
  // do something with the form data
  $insert = query("INSERT INTO expenses (car, amount, date_and_time, type, input_by, paid, comments) VALUES (?,?,?,?,?,?,?)", $car, $amount, $datetime, $type, $input_by, $paid, $comments);
  if (!empty($insert)) {
  	echo "Succsess!";
  } else {
  	echo "Error";
  }
  
}



?>