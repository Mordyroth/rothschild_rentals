<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(10000);
require 'functions.php';

upload_hq_reservations();

delete_duplicate_ezpass();

//match_up_ezpass();

import_supercharger();

match_up_superchargers();


match_up_superchargers_turo();

post_supercharger_charges();

add_ezpass_and_supercharger();
//post_ezpass_charges();

//chargeTransponderRental();


?>