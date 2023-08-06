<!DOCTYPE html>
<html>
<head>
    <script src="status.js"></script> <!-- Include the status.js file -->
</head>
<body>
heyppp
<form action="lock_car.php" method="post">
    <!-- Repeat this pattern for each car, replacing the name, VIN, and odometer ID -->
    Elias 594 Gray: <span id="odometer_KMHLS4AGXPU595867"></span> <button type="submit" name="vin" value="KMHLS4AGXPU595867">Lock</button><br>
    Elon 579 Gray: <span id="odometer_KMHLS4AG5PU567118"></span> <button type="submit" name="vin" value="KMHLS4AG5PU567118">Lock</button><br>
    <!-- ... -->
</form>

<form action="unlock_car.php" method="post">
    <!-- Repeat this pattern for each car, replacing the name, VIN, and odometer ID -->
    Elias 594 Gray: <span id="odometer_KMHLS4AGXPU595867"></span> <button type="submit" name="vin" value="KMHLS4AGXPU595867">Unlock</button><br>
    Elon 579 Gray: <span id="odometer_KMHLS4AG5PU567118"></span> <button type="submit" name="vin" value="KMHLS4AG5PU567118">Unlock</button><br>
    <!-- ... -->
</form>

<script>
    // JavaScript code to fetch the odometer readings for each car
    // Replace the VINs with the actual VINs for your cars
    fetchOdometer('KMHLS4AGXPU595867');
    fetchOdometer('KMHLS4AG5PU567118');
    // ...
</script>

</body>
</html>