const BlueLinky = require('bluelinky');

const client = new BlueLinky({
  username: process.env.BLUELINKY_USERNAME || "mordy@rothschildrentals.com",
  password: process.env.BLUELINKY_PASSWORD || "$Mordyroth0430",
  region: "US",
  pin: "8500",
});

const vinArg = process.argv[2]; // Get the VIN from the command-line argument

client.on('ready', async () => {
  const vehicles = await client.getVehicles();
  vehicles.forEach(async (vehicle) => {
    if (vehicle.vin === vinArg) { // Check if the VIN matches the argument
      const status = await vehicle.status();
      console.log(status.odometer); // Print the odometer reading
    }
  });
});
