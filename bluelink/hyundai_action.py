from hyundai_kia_connect_api.hyundai_kia_connect_api import HyundaiBlueLinkAPIUSA

def main():
    # Initialize the API with your credentials and region
    api = HyundaiBlueLinkAPIUSA('mordy@rothschildrentals.com', '$Mordyroth0430', '8500')

    # Authenticate with the API
    api.authenticate()

    # Get the vehicle status
    vehicle_status = api.get_vehicle_status('KMHLS4AGXPU595867')

    # Lock the vehicle
    api.lock_vehicle('KMHLS4AGXPU595867')

if __name__ == "__main__":
    main()
