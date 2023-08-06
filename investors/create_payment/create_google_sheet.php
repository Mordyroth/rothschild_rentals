<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$myEmail = 'mordy@mrst.co';
require '../functions.php';
require_once '../google-api-php-client/vendor/autoload.php';

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_RowData;
use Google_Service_Sheets_CellData;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Oauth2;
use Google_Service_Drive_Permission;

$investors = query("SELECT * FROM Investors");
$current_month = date('m');
$current_year = date('Y');

if ($current_month == 1) {
    $previous_month = 12;
    $previous_year = $current_year - 1;
} else {
    $previous_month = $current_month - 1;
    $previous_year = $current_year;
}

$start_date = $previous_year . '-' . str_pad($previous_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00';
$end_date = $current_year . '-' . str_pad($current_month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00';

foreach ($investors as $investorRow) {

    //FOR TESTING!!!
    //if ($investorRow['name'] != "Rimmer") {
     //   continue;
    //}
    ///END TESTING
    $investorRow['insurance_bef_split'] = (bool) $investorRow['insurance_bef_split'];
    $expenses = query("SELECT * FROM expenses WHERE (date_and_time >= ? AND date_and_time < ?) AND car = ? ORDER BY date_and_time DESC", $start_date, $end_date, $investorRow['car_name']);

    $monthlyCharges = [];
    foreach ($investorRow as $column => $value) {
        if (strpos($column, 'monthly_expense_') === 0) {
            $key = str_replace('monthly_expense_', '', $column);
            $monthlyCharges[$key] = $value;
        }
    }

    $expensesArray = ['monthly_expenses' => $monthlyCharges];

    $investor = $investorRow['name'];

    // Set up the Google API client
    $client = new Google_Client();
    $client->setApplicationName("Create Google Sheet in Folder");
    $client->setScopes([
        Google_Service_Sheets::SPREADSHEETS,
        Google_Service_Drive::DRIVE,
        Google_Service_Oauth2::USERINFO_EMAIL
    ]);
    $client->setAuthConfig('/var/www/html/rothschild_rentals/investors/create_payment/sheets_credentials.json');
    $client->setAccessType('offline');

    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    $sheetsService = new Google_Service_Sheets($client);
    $spreadsheet = new Google_Service_Sheets_Spreadsheet([
        'properties' => [
            'title' => 'Temporary Sheet'
        ]
    ]);

    $spreadsheet = $sheetsService->spreadsheets->create($spreadsheet, [
        'fields' => 'spreadsheetId'
    ]);

    $previousMonth = date('Y-m', strtotime('first day of previous month'));
    $sheetName = "{$investor}-{$previousMonth}";
    $driveService = new Google_Service_Drive($client);
    $renameFile = new Google_Service_Drive_DriveFile([
        'name' => $sheetName
    ]);
    $driveService->files->update($spreadsheet->spreadsheetId, $renameFile);

    $oauth2Service = new Google_Service_Oauth2($client);
    $userInfo = $oauth2Service->userinfo->get();
    $userEmail = $userInfo->getEmail();

    echo "Google Account Email: " . $userEmail . "\n";

    try {
        $emptyFileMetadata = new Google_Service_Drive_DriveFile();
        $sheetUpdateResult = $driveService->files->update($spreadsheet->spreadsheetId, $emptyFileMetadata, [
            'addParents' => $investorRow['drive_payments_folder_id'],
            'removeParents' => 'root',
            'fields' => 'id, parents'
        ]);
        echo "Google Sheet created and moved to the target folder successfully!";
    } catch (Exception $e) {
        echo 'Error: ', $e->getMessage(), "\n";
    }

    try {
        $rows = [];
        $rows[] = new Google_Service_Sheets_RowData([
    'values' => [
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Last Name'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'HQ ID'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Reservation Link'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Rack Price'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Discounts Amount'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'External Charges Price'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Total Price'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Pickup Date'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Return Date'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Status'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Updated On'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Multiple Vehicles'
            ]
        ]),
        
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Notes'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Total Tolls'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Total Supercharger'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Rental Days'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Total Miles'
            ]
        ]),
         new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'Vehicle Key'
            ]
        ]),
        new Google_Service_Sheets_CellData([
            'userEnteredValue' => [
                'stringValue' => 'First Name'
            ]
        ]),
        // Add more columns as needed, following the pattern above.
    ]
]);


        // New code starts here:

        $reservations = query("SELECT * FROM reservations WHERE (pickup_date <= ? AND return_date >= ?) AND vehicle_key = ? ORDER BY pickup_date DESC", $end_date, $start_date, $investorRow['car_name']);


        if ($reservations) {
            foreach ($reservations as $reservation) {
                $rows[] = new Google_Service_Sheets_RowData([
                    'values' => [
                        new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['last_name']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'numberValue' => $reservation['hq_id']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'formulaValue' => '=HYPERLINK("https://rothschild-rentals.us5.hqrentals.app/car-rental/reservations/step9?id='.$reservation['hq_id'].'", "Reservation Link")'
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'numberValue' => $reservation['rack_price']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'numberValue' => $reservation['discounts_amount']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'numberValue' => $reservation['external_charges_price']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'numberValue' => $reservation['total_price']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['pickup_date']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['return_date']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['status']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['updated_on']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['multiple_vehicles']
                ]
            ]),
            
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['notes']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['total_tolls']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['total_supercharger']
                ]
            ]),
             new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['rental_days']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['total_miles']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['vehicle_key']
                ]
            ]),
            new Google_Service_Sheets_CellData([
                'userEnteredValue' => [
                    'stringValue' => $reservation['first_name']
                ]
            ]),
            // Add more columns as needed, following the pattern above.
                    ]
                ]);
            }
        }

        // New code ends here.

        $rows[] = new Google_Service_Sheets_RowData(); // Blank line
        
        if ($expenses) {
            $rows[] = new Google_Service_Sheets_RowData(); // Blank line
            $rows[] = new Google_Service_Sheets_RowData([
                'values' => [
                    new Google_Service_Sheets_CellData([
                        'userEnteredValue' => [
                            'stringValue' => 'Expenses'
                        ]
                    ])
                ]
            ]);

            foreach ($expenses as $expense) {
                $dateValue = date('Y-m-d', strtotime($expense['date_and_time']));
                $rows[] = new Google_Service_Sheets_RowData([
                    'values' => [
                        new Google_Service_Sheets_CellData([
                            'userEnteredValue' => [
                                'stringValue' => $expense['date_and_time']
                            ]
                        ]),
                        new Google_Service_Sheets_CellData([
                            'userEnteredValue' => [
                                'numberValue' => -$expense['amount']
                            ]
                        ]),
                        new Google_Service_Sheets_CellData([
                            'userEnteredValue' => [
                                'stringValue' => $expense['comments']
                            ]
                        ])
                    ]
                ]);
            }

            $rows[] = new Google_Service_Sheets_RowData(); // Blank line
        }
        $rows[] = new Google_Service_Sheets_RowData([
            'values' => [
                new Google_Service_Sheets_CellData([
                    'userEnteredValue' => [
                        'stringValue' => 'monthly_expenses'
                    ]
                ])
            ]
        ]);

        $managementFeeRow = new Google_Service_Sheets_RowData([
            'values' => [
                new Google_Service_Sheets_CellData([
                    'userEnteredValue' => [
                        'stringValue' => '40% Management Fee'
                    ]
                ])
            ]
        ]);
        $insuranceRow = NULL;

        foreach ($expensesArray['monthly_expenses'] as $key => $value) {
            if ($key === "insurance") {
                $insuranceKey = $investorRow['insurance_bef_split'] ? "insurance calculated bef split" : "insurance calculated after split";
                $insuranceRow = new Google_Service_Sheets_RowData([
                    'values' => [
                        new Google_Service_Sheets_CellData([
                            'userEnteredValue' => [
                                'stringValue' => $insuranceKey
                            ]
                        ]),
                        new Google_Service_Sheets_CellData([
                            'userEnteredValue' => [
                                'numberValue' => -$value
                            ]
                        ])
                    ]
                ]);

                if ($investorRow['insurance_bef_split']) {
                    $rows[] = new Google_Service_Sheets_RowData(); // Blank line
                    $rows[] = $insuranceRow;
                    $rows[] = $managementFeeRow;
                }
            } else {
                $rows[] = new Google_Service_Sheets_RowData([
                    'values' => [
                        new Google_Service_Sheets_CellData([
                            'userEnteredValue' => [
                                'stringValue' => $key
                            ]
                        ]),
                        new Google_Service_Sheets_CellData([
                            'userEnteredValue' => [
                                'numberValue' => -$value
                            ]
                        ])
                    ]
                ]);
            }
        }

        if (!$investorRow['insurance_bef_split'] && $insuranceRow !== NULL) {
            $rows[] = new Google_Service_Sheets_RowData(); // Blank line
            $rows[] = $managementFeeRow;
            $rows[] = new Google_Service_Sheets_RowData(); // Blank line
            $rows[] = $insuranceRow;
        } else if (!array_key_exists("insurance", $expensesArray['monthly_expenses'])) {
            $rows[] = new Google_Service_Sheets_RowData(); // Blank line
            $rows[] = $managementFeeRow;
            $rows[] = new Google_Service_Sheets_RowData(); // Blank line
        }

        $requestBody = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [
                [
                    'updateCells' => [
                        'start' => [
                            'sheetId' => 0,
                            'rowIndex' => 0,
                            'columnIndex' => 0
                        ],
                        'rows' => $rows,
                        'fields' => 'userEnteredValue'
                    ]
                ]
            ]
        ]);

        $sheetsService->spreadsheets->batchUpdate(
            $spreadsheet->spreadsheetId,
            $requestBody
        );

        echo "Values written to Google Sheet successfully!";
    } catch (Exception $e) {
        echo 'Error: ', $e->getMessage(), "\n";
    }

    $permission = new Google_Service_Drive_Permission([
        'type' => 'user',
        'role' => 'writer',
        'emailAddress' => $myEmail
    ]);
    $driveService->permissions->create($spreadsheet->spreadsheetId, $permission);

    echo "Permission given to " . $myEmail . "!\n";
}
