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

function formatAddress($address) {
    // Split the address into parts
    $address_parts = explode(', ', $address);

    // Assign default values and extract relevant parts
    $street = isset($address_parts[0]) ? $address_parts[0] : '';
    $city = isset($address_parts[1]) ? $address_parts[1] : '';
    $state_zip = isset($address_parts[2]) ? $address_parts[2] : '';
    $country = isset($address_parts[3]) ? $address_parts[3] : '';

    // Split state and zip code, if present
    $state = $zip = '';
    if ($state_zip) {
        preg_match('/(.*\D)(\d+)/', $state_zip, $state_zip_parts);
        $state = isset($state_zip_parts[1]) ? trim($state_zip_parts[1]) : '';
        $zip = isset($state_zip_parts[2]) ? $state_zip_parts[2] : '';
    }

    // Process city and street
    $city = $city ? str_ireplace('township', '', $city) : '';
    $city = trim($city);
    $street_name = $street ? preg_replace('/^[\d-]+ /', '', $street) : '';
    $street_name = $street_name ? abbreviateDirection($street_name) : '';

    // Check if the address is in the United States
    if ($country === 'United States' || $country === '') {
        // Shorten the state name, if available
        $state_short = $state ? stateToAbbreviation($state) : '';
        // Construct and return formatted address
        return trim("$street_name, $city, $state_short", ', ');
    } else {
        // Return formatted address including the country
        return trim("$street_name, $city, $country", ', ');
    }
}


function abbreviateDirection($string) {
    $directions = array(
        'north' => 'N',
        'south' => 'S',
        'east' => 'E',
        'west' => 'W'
    );

    foreach ($directions as $word => $abbreviation) {
        $string = preg_replace("/\b" . preg_quote($word, "/") . "\b/i", $abbreviation, $string);
    }

    return $string;
}

function stateToAbbreviation($state) {
    $states = array(
        'Alabama'=>'AL',
        'Alaska'=>'AK',
        'Arizona'=>'AZ',
        'Arkansas'=>'AR',
        'California'=>'CA',
        'Colorado'=>'CO',
        'Connecticut'=>'CT',
        'Delaware'=>'DE',
        'Florida'=>'FL',
        'Georgia'=>'GA',
        'Hawaii'=>'HI',
        'Idaho'=>'ID',
        'Illinois'=>'IL',
        'Indiana'=>'IN',
        'Iowa'=>'IA',
        'Kansas'=>'KS',
        'Kentucky'=>'KY',
        'Louisiana'=>'LA',
        'Maine'=>'ME',
        'Maryland'=>'MD',
        'Massachusetts'=>'MA',
        'Michigan'=>'MI',
        'Minnesota'=>'MN',
        'Mississippi'=>'MS',
        'Missouri'=>'MO',
        'Montana'=>'MT',
        'Nebraska'=>'NE',
        'Nevada'=>'NV',
        'New Hampshire'=>'NH',
        'New Jersey'=>'NJ',
        'New Mexico'=>'NM',
        'New York'=>'NY',
        'North Carolina'=>'NC',
        'North Dakota'=>'ND',
        'Ohio'=>'OH',
        'Oklahoma'=>'OK',
        'Oregon'=>'OR',
        'Pennsylvania'=>'PA',
        'Rhode Island'=>'RI',
        'South Carolina'=>'SC',
        'South Dakota'=>'SD',
        'Tennessee'=>'TN',
        'Texas'=>'TX',
        'Utah'=>'UT',
        'Vermont'=>'VT',
        'Virginia'=>'VA',
        'Washington'=>'WA',
        'West Virginia'=>'WV',
        'Wisconsin'=>'WI',
        'Wyoming'=>'WY'
    );

    if (isset($states[$state])) {
        return $states[$state];
    } else {
        return $state;
    }
}

?>