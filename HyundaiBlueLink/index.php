<!DOCTYPE html>
<html>
<head>
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
function fetchOdometer(vin) {
  fetch(`get_odometer.php?vin=${vin}`)
    .then(response => response.text())
    .then(odometer => {
      document.getElementById(`odometer_${vin}`).innerText = `Odometer: ${odometer}`;
    })
    .catch(error => console.error('Error fetching odometer:', error));
}

// Fetch the odometer readings for each car
fetchOdometer('KMHLS4AGXPU595867');
fetchOdometer('KMHLS4AG5PU567118');
// ...
</script>


</body>
</html>
