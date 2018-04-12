import googlemaps
import csv
import re
import os

resorts_file = 'scraping/resort2.info'
airports_file = 'scraping/flight_data/airports_iata2.csv'
previous_resorts_airports_file = 'resort_airports.out'
resorts_airports_file = 'resort_airports.out'
gmaps_key = os.environ.get('GMAPS_KEY')

state_international_airports = {}
airports = []
failed_resorts = []
resorts = {}

gmaps = googlemaps.Client(key=gmaps_key)

def get_state_international_airport(state):
    # Edge cases where the returned google result name has a different airport name than the one specified in the ita airport data
    if state == 'vermont':
        return 'Burlington International Airport'
    elif state == 'washington':
        return 'Seattle Tacoma International Airport'
    elif state == 'maine':
        return 'Portland International Airport'
    
    if not state in state_international_airports:
        state_airport_result = gmaps.places(query='international airport near {0} state'.format(resort['state']))
        state_airport = state_airport_result['results'][0]
        state_international_airports[state] = state_airport['name']

    return state_international_airports[state]

def get_iata_code(airport_name):
    name = airport_name

    ita_matches_by_name = [airport['iata_code'] for airport in airports if re.match(name, airport['name'], re.I)][:1]

    if ita_matches_by_name:
        return ita_matches_by_name[0]
    else:
        return ''

def ensure_set_lat_lng(resort):
    if not resort['lat'] or not resort['lng']:
        geocode_result = gmaps.geocode(address=resort['resort'])

        if geocode_result:
            location = geocode_result[0]['geometry']['location']
            resort['lat'] = location['lat']
            resort['lng'] = location['lng']
        else:
            print("Unable to lookup {}".format(resort['resort']))
    
    return resort

def ensure_set_airport(resort):
    if not resort['airport']:
        lat_long = "{0},{1}".format(resort['lat'], resort['lng'])
        airport_result = gmaps.places(query='public airport',location=lat_long, type='airport')

        if airport_result['status'] == 'OK':
            airport = airport_result['results'][0]
            airport_name = airport['name']

            resort['airport'] = airport_name
        else:
            print("Unable to find nearest airport for {}".format(resort['resort']))    
    
    return resort

def ensure_set_airport_iata(resort):
    if not resort['airport_iata']:
        iata_code = get_iata_code(resort['airport'])

        if not iata_code:
            # No iata code, pick closest one from state
            state_airport = get_state_international_airport(resort['state'])
            resort['airport'] = state_airport
            iata_code = get_iata_code(state_airport)
            
        resort['airport_iata'] = iata_code

        if not resort['airport_iata']:
            print("Unable to find get airport iata for {}, {}".format(resort['resort'], resort['airport']))    

    return resort


resorts_reader = csv.reader(open(resorts_file, 'r'))

for row in resorts_reader:
    resort_name = row[0]
    state = row[-1]

    resorts[resort_name] = {
        'resort': resort_name,
        'lat':'',
        'lng':'',
        'airport':'',
        'airport_iata':'',
        'state': state
    }

airports_reader = csv.reader(open(airports_file, 'r', encoding='utf-8'))

for row in airports_reader:
    airports.append({'name': row[0], 'lat':row[1], 'lng':row[2], 'iata_code': row[3]})

if os.path.exists(previous_resorts_airports_file):
    previous_resort_airports_file_reader = csv.reader(open(previous_resorts_airports_file, 'r', encoding='utf-8'))
    previous_results = []
    i = 1

    for row in previous_resort_airports_file_reader:
        # Skip header
        if i > 1:
            previous_results.append({'resort': row[0], 'lat':row[1], 'lng':row[2], 'airport':row[3], 'airport_iata':row[4], 'state':row[5]})

        i += 1

    for previous_result in previous_results:
        previous_result_resort_name = previous_result['resort']
        if previous_result_resort_name in resorts:
            resorts[previous_result_resort_name] = previous_result

resort_airports_file_writer = csv.DictWriter(
    open(resorts_airports_file, 'w', newline='', buffering=1),
    fieldnames=('resort','lat','lng','airport','airport_iata', 'state'))
resort_airports_file_writer.writeheader()

for resort_name, resort in resorts.items():
    ensure_set_lat_lng(resort)
    ensure_set_airport(resort)
    ensure_set_airport_iata(resort)

    resort_airports_file_writer.writerow(resort)
        