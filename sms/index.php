<Response>
<?php
ini_set('display_errors',1);ini_set('display_startup_errors',1);error_reporting(E_ALL);

require 'functions.php';
//query("INSERT INTO logs (phone, status) VALUES(?,?)", $_REQUEST['Body'], $_REQUEST['From']);

if (empty($_REQUEST['Body'])) {
	exit;
} 



	
echo Message('We are only accepting reservations thru the phone system. Please call. If you have an issue you can call or text 848-373-8500', $_REQUEST['From']);

?>
</Response>