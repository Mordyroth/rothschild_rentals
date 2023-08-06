from hyundaikiaconnect import HyundaiBlueLink

# Replace these with your actual credentials
username = 'mordy@rothschildrentals.com'
password = '$Mordyroth0430'
region = 'us'  # Replace with your region code (e.g., 'us', 'eu', 'cn', etc.)

# Initialize the HyundaiBlueLink object
client = HyundaiBlueLink(username=username, password=password, region=region)

# Login to the service
client.login()

# Get all vehicles associated with the account
vehicles = client.vehicles()
for vehicle in vehicles:
    print(f"Vehicle Name: {vehicle.name}")
    print(f"VIN: {vehicle.vin}")
    print(f"Year: {vehicle.year}")
    print(f"Model: {vehicle.model}")
    print(f"Is Connected: {vehicle.is_connected}")
    print()

# Example: Lock and Unlock the first vehicle
if vehicles:
    first_vehicle = vehicles[0]
    print("Locking the first vehicle...")
    first_vehicle.lock()
    print("Vehicle locked.")
    print("Unlocking the first vehicle...")
    first_vehicle.unlock()
    print("Vehicle unlocked.")
