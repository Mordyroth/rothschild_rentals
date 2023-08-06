<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <title>Car Transactions</title>
</head>
<body>
    <div class="container">
        <h1>Car Transactions</h1>
        <?php include 'populate_matchup_page.php'; ?>
        <!-- Add a script tag to set a global variable with the PHP data -->
  <!-- Data assignment -->
<script>
    const jsonString = '<?php echo addslashes(json_encode($array)); ?>';
    const data = JSON.parse(jsonString);
    console.log('Data:', data);
</script>

        <table>
            <thead>
                <tr>
                    <th>Nickname</th>
                    <th>Date</th>
                    <th>Tag/Plate</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <!--<th>VIN</th>-->
                    <th>Location</th>
                    <th>ID</th>
                    <!--<th>Description</th>
                    <th>Invoice</th>-->
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($array as $item):
                ?>
                    <tr>
                        <td><?php echo $item['car_nickname']; ?></td>
                        <td><?php  $transactionFormatDate = new DateTime($item['transaction_format_date']);
        echo $transactionFormatDate->format('l, m/d g:i A');//echo $item['transaction_format_date']; ?></td>
                        <td><?php echo $item['tag_plate'] ?? ''; ?></td>
                        <td><?php echo $item['type']; ?></td>
                        <td><?php echo $item['amount']; ?></td>
                        <!--<td><?php //echo $item['vin']; ?></td>-->
                        <td><?php echo $item['location_abbr'] ?? ''; ?></td>
                        <td><?php echo $item['row_id'] ?? ''; ?></td>
                        <!--<td><?php //echo $item['description'] ?? ''; ?></td>-->
                        <!--<td><?php //echo $item['invoice'] ?? ''; ?></td>-->
                       <td>
                          <button class="trips-button"
                                  data-row_id="<?php echo $item['row_id']; ?>"
                                  data-car_nickname="<?php echo htmlspecialchars($item['car_nickname']); ?>"
                                  data-transaction_format_date="<?php echo htmlspecialchars($item['transaction_format_date']); ?>"
                                  data-type="<?php echo htmlspecialchars($item['type']); ?>"
                                  data-amount="<?php echo htmlspecialchars($item['amount']); ?>"
                                  data-location_abbr="<?php echo !empty($item['location_abbr']) ? htmlspecialchars($item['location_abbr']) : ''; ?>">
                            Trips
                          </button>
                        </td>

                        <td><button class="update-button" data-row_id="<?php echo $item['row_id']; ?>">Bill To Investor</button></td>
                        <!-- Add a hidden element to store the additional values -->
                        <td style="display: none;">
                            <?php echo htmlentities(json_encode($item['prev_trips'] ?? [])); ?>
                        </td>
                        <td style="display: none;">
                            <?php echo htmlentities(json_encode($item['after_trips'] ?? [])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
        <!-- Add the modal structure -->
        <div class="modal" id="detailsModal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="prevTrips"></div>
                <div id="afterTrips"></div>
            </div>
        </div>
    </div>
<script src="main.js"></script>
</body>
</html>
