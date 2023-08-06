const BlueLinky = require('bluelinky');

const client = new BlueLinky({
  username: "mordy@rothschildrentals.com",
  password: "$Mordyroth0430",
  region: "US",
  pin: "8500",
});

client.on('ready', async () => {
  const vehicles = await client.getVehicles();
  vehicles.forEach(async (vehicle) => {
    const status = await vehicle.status();
    console.log(`Odometer: ${status.odometer}`); // Access the odometer information
  });
});
