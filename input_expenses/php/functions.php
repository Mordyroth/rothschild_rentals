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
        //return $results;
        trigger_error($statement->errorInfo()[2], E_USER_ERROR);
        return false;
        exit;
    }
    // return result set's rows
   return $results;
    //return $statement->fetchAll(PDO::FETCH_ASSOC);
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

?>