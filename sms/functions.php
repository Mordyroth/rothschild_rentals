<?php
require_once '/var/www/html/vendor/autoload.php';
use Twilio\Rest\Client;
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


define("TWILIO_SID", "AC5a030c6fa5c66e2ff79132d9e4b4dac3");

define("TWILIO_TOKEN", "8cfac40e73434af71686e475e35cf923");
define('OUR_PHONE_NUMBER', '+19803723274');


   

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

function Message($text, $to) {
    $client = new Client(TWILIO_SID, TWILIO_TOKEN);
    try
      {
          $client->messages
            ->create(
                $to,
                array(
                    "from" => "+17323336252",
                    "body" => $text
                )
            );
            //query("UPDATE past_mobile_nums SET num = ? WHERE num1 = ?", 'done', $to);
      }
      catch (Exception $e)
      {
            //query("UPDATE past_mobile_nums SET num = ? WHERE num1 = ?", 'error', $to);
          echo 'error <br/>';   
      }
    
}

?>

