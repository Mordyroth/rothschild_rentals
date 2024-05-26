<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(10000);
require 'functions.php';
// Define the upload directory
$upload_dir = 'ezpass/';

// Define the file size limit in bytes (5MB)
$file_size_limit = 5 * 1024 * 1024;

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Check if the file was uploaded without errors
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        // Check if the file is a CSV file
        $file_type = mime_content_type($_FILES['file']['tmp_name']);
        $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if ($file_ext != 'csv') {
            echo $file_type;
            echo '  Error: Only CSV files are allowed.';
            exit();
        }

        // Check if the file is smaller than the file size limit
        $file_size = $_FILES['file']['size'];
        if ($file_size > $file_size_limit) {
            echo 'Error: The file size limit is 5MB.';
            exit();
        }

        // Move the uploaded file to the upload directory
        $file_name = basename($_FILES['file']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            echo 'The file was uploaded successfully.';
            $handle = fopen($file_path, 'r');

            // Check if the file was opened successfully
            if (!$handle) {
                die('Unable to open file');
            }

            // Get the first row as column names
            $columns = fgetcsv($handle);

            // Check if the file is empty
            if (!$columns) {
                die('File is empty');
            }

            // Initialize the data array
            $data = array();

            // Loop through the remaining rows and read each line
            while ($row = fgetcsv($handle)) {
                // Combine the row values with the column names and add to the data array
                $data[] = array_combine($columns, $row);
            }

            // Check if any data was read from the file
            if (count($data) == 0) {
                die('No data found in file');
            }

            $amountAlreadyExsists = 0;
            foreach ($data as $value) {
                $check = query_ezpass("SELECT * FROM ezpass WHERE transaction_date = ? AND exit_time = ? AND amount = ? AND tag_plate = ?", $value['TRANSACTION DATE'], $value['EXIT TIME'], $value['AMOUNT'], $value['TAG/PLATE NUMBER']);
                if ($check) {
                    $amountAlreadyExsists++;
                } else {
                            $value['transaction_format_date'] = "";
                            if (!empty($value['TRANSACTION DATE']) && !empty($value['EXIT TIME'])) {
                                $value['transaction_format_date'] = convertDateTime($value['TRANSACTION DATE'], $value['EXIT TIME']);
                            } 
                                
                                $key_map = array(
                            'POSTING DATE' => 'posting_date',
                            'TRANSACTION DATE' => 'transaction_date',
                            'TAG/PLATE NUMBER' => 'tag_plate',
                            'AGENCY' => 'agency',
                            'DESCRIPTION' => 'description',
                            'ENTRY PLAZA' => 'plaza_id',
                            'ENTRY TIME' => 'entry_time',
                            'ENTRY LANE' => 'entry_lane',
                            'EXIT TIME' => 'exit_time',
                            'EXIT PLAZA' => 'exit_plaza',
                            'EXIT LANE' => 'exit_lane',
                            'VEHICLE TYPE CODE' => 'vehicle_type_code',
                            'AMOUNT' => 'amount',
                            'PREPAID' => 'prepaid',
                            'PLAN/RATE' => 'plan_rate',
                            'FARE TYPE' => 'fare_type',
                            'BALANCE' => 'balance'
                        );

                        // Rename keys in input array
                        $output_array = array_combine(
                            array_map(function ($key) use ($key_map) {
                                return $key_map[$key] ?? $key;
                            }, array_keys($value)),
                            array_values($value)
                        );
                        insert_row("ezpass", $output_array);
                        $notExsists[] = $output_array;
                }
            }
            match_up_ezpass();
            post_ezpass_charges();
            chargeTransponderRental();
            //match_up_ezpass_turo();
            echo "Number of ezpass entries that already exsists in the ezpass table: " . $amountAlreadyExsists;
            // Close the file
            fclose($handle);

        } else {
            echo 'Error: Failed to upload file.';
        }
    } else {
        echo 'Error: Failed to upload file.';
    }
}

unlink($file_path);

add_ezpass_and_supercharger();

function insert_row($table, $fields_values) {
    // Extract fields and values from input array
    $fields = array_keys($fields_values);
    $values = array_values($fields_values);
    
    // Build the query
    $placeholders = array_fill(0, count($fields), '?');
    $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
    
    // Execute the query
    $results = query_ezpass($sql, ...$values);
    
    // Return the result
    return $results;
}

?>
<pre><?php 
if (!empty($notExsists)) { 
    print_r($notExsists); 
} else {
    echo "No ezpass entries added. All entries already exsists.";
} 
?></pre>