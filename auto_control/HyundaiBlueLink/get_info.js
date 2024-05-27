const BlueLinky = require("bluelinky");

const client = new BlueLinky({
  username: "mordy@rothschildrentals.com",
  password: "$Mordyroth0430",
  region: "US",
  pin: "8500",
});

client.on("ready", async () => {
  try {
    const vehicle = await client.getVehicle(process.argv[2]);
    if (!vehicle) {
      throw new Error("Vehicle not found");
    }

    // Get location
    const location = await vehicle.location();

    // Get odometer
    const odometer = await vehicle.odometer();

    // Get vehicle status to fetch fuel level
    const status = await vehicle.status();
    console.log('Full Status:', JSON.stringify(status, null, 2)); // Debugging line

    let fuelLevel = "N/A";
    if (status && status.engineStatus && typeof status.engineStatus.fuelLevel !== 'undefined') {
      fuelLevel = status.engineStatus.fuelLevel;
    }

    // Output all data as JSON
    const output = {
      location: location,
      odometer: odometer,
      fuelLevel: fuelLevel
    };

    console.log(JSON.stringify(output, null, 2)); // Pretty-print JSON for debugging

  } catch (error) {
    console.error("Error fetching vehicle data:", error.message);
    console.log(JSON.stringify({ error: error.message }));
  }
});

client.on("error", (error) => {
  console.error("Error connecting to BlueLinky:", error.message);
});
