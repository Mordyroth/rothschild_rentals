const BlueLinky = require("bluelinky");

const client = new BlueLinky({
  username: "mordy@rothschildrentals.com",
  password: "$Mordyroth0430",
  region: "US",
  pin: "8500",
});

client.on("ready", async () => {
  const vehicle = client.getVehicle(process.argv[2]);
  const status = await vehicle.status();
  console.log(status);
});
