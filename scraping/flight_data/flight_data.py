from datetime import timedelta, date
from dateutil.relativedelta import relativedelta
import requests
import csv
import os

def month_range(start_date, end_date):
    i = 0
    while start_date + relativedelta(months=i) <= end_date:
        yield start_date + relativedelta(month=i)

def get_flight_price(origin, destination, depart_date, return_date):
    parameters = "?origin={origin}&destination={destination}&depart_date={depart_date}$return_date={return_date}&currency=USD&token={token}".format(
        origin=origin.upper(),
        destination=destination.upper(),
        depart_date=depart_date.strftime('%Y-%m'),
        return_date=return_date.strftime('%Y-%m'),
        token=token
    )

    query_url = flightdata_endpoint + parameters
    response = requests.get(query_url)

    return response.json()

def get_resort_airports(file):
    airports = set()

    with open(resort_airports_file, 'r') as resort_airport_file:
        reader = csv.reader(resort_airport_file)
        is_header = True 

        for row in reader:
            if is_header:
                airport_iata_index = [index for index, column_name in enumerate(row) if column_name == 'airport_iata'][0]
                is_header = False
            else:
                airports.add(row[airport_iata_index])

    return airports


# Flight pricing end point used
flightdata_endpoint = "http://api.travelpayouts.com/v1/prices/cheap"
token = os.environ.get('TRAVELPAYOUTS_TOKEN')
resort_airports_file = "scraping/flight_data/resort_airports.out"
flight_prices_file = "flight_data.out"

# Flights that depart between the months of these will be obtained
departure_start_date = date(2018, 4, 1)
departure_end_date = date(2018, 5, 1)

# Top 3 cheapest flights
max_results_per_search = 5000

departing_airports_iata = [
    # New York
    "JFK",
    # Chicago
    "ORD",
    # Los Angeles
    "LAX",
    # Seattle
    "SEA",
    # Dallas
    "DFW",
    # Atlanta
    "ATL"
]

resort_airports = get_resort_airports(resort_airports_file)
flight_prices_writer = csv.DictWriter(
    open(flight_prices_file, 'w', newline=''),
    fieldnames=('origin', 'destination', 'departure_date', 'return_date', 'price', 'airline', 'flight_number'))
flight_prices_writer.writeheader()

for departure_date in month_range(departure_start_date, departure_end_date):
    for return_date in month_range(departure_date, departure_end_date):
        for departing_airport in departing_airports_iata:
            for resort_airport in resort_airports:
                flight_pricing_results = get_flight_price(origin=departing_airport, destination=resort_airport, depart_date=departure_date, return_date=return_date)

                if flight_pricing_results and flight_pricing_results['success']:
                    if flight_pricing_results['data'] and flight_pricing_results['data']['USD']:
                        flight_prices = flight_pricing_results['data']['USD']
                        
                        for index, flight_price in flight_prices:
                            if index > max_results_per_search:
                                break
                            else:
                                flight_price_info = {
                                    'airline' : flight_price['airline'],
                                    'price' : flight_price['price'],
                                    'flight_number' : flight_price['flight_number'],
                                    'depature_date' : departure_date,
                                    'return_date' : return_date,
                                    'origin' : departing_airport,
                                    'destination' : resort_airport
                                }

                                flight_prices_writer.writerow(flight_price_info)







