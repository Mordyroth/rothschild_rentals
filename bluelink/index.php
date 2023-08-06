<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_id = $_POST["vehicle_id"];
    $action = $_POST["action"];
    
    // Run the Python script and pass in the vehicle ID and action
    $command = escapeshellcmd("python3 /var/www/html/rothschild_rentals/bluelink/hyundai_action.py $vehicle_id $action");
    $output = shell_exec($command);
    
    echo $output;
}
?>

<form method="post">
    Vehicle ID: <input type="text" name="vehicle_id"><br>
    Action: <select name="action">
        <option value="lock">Lock</option>
        <option value="unlock">Unlock</option>
    </select><br>
    <input type="submit">
</form>

