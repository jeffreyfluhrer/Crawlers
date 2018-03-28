import csv
import re
import random

# This script will produce three files that will be the csv inputs for the Resort, StayPricing and
# WeatherForecast tables.
# Resort schema is Resort(ResortName, City, State, SeasonStartDAte, SeasonEndDate, ImageuRL, rating, difficulty)
# StayPricing schema is StayPricing (Resortname, Date, StayPrice, LiftTicketPrice, BookingURL)
# WeatherForceast schema is WeatherForecast (ResortName, Date, SnowDepth, Status)

def massage(file1):
    reader1 = csv.reader(open(file1, 'r'))
    resortCSV = csv.writer(open('resort.out','w'))
    stayCSV = csv.writer(open('staypricing.out','w'))
    weatherCSV = csv.writer(open('weatherforecast.out','w'))
    flightCSV = csv.writer(open('flight.out','w'))    

    # Define the lists
    Resort = []
    ticketPrice = []
    beginner = []
    intermed = []
    advanced = []
    season = []
    rating = []
    image = []
    updateDate = []
    snowDepth = []
    status = []    
    
    # Read all the file data into memory
    for row in reader1:
        Resort.append(row[0])
        ticketPrice.append(row[1])
        beginner.append(row[2])
        intermed.append(row[3])
        advanced.append(row[4])
        season.append(row[5])
        rating.append(row[6])
        image.append(row[7])
        updateDate.append(row[8])
        snowDepth.append(row[9])
        status.append(row[10])

    # Massage data for Resort table
    Resort = manipResort(Resort)
    ticketPrice = manipPrice(ticketPrice)
    difficulty = manipDifficulty(beginner, intermed, advanced)
    startSeason = manipStartSeason(season)
    endSeason = manipEndSeason(season)
    rating = manipRating(rating)
    location = createLocations(Resort)

    # Output data for the Resort table
    writeResort(resortCSV, Resort, ticketPrice, difficulty, startSeason, endSeason, image, rating, location)

    # Massage the weather forecast data
    weatherDate = manipUpdateDate(updateDate)
    snowDepth = manipSnowDepth(snowDepth)
    status = manipStatus(status)

    # Output data for the WeatherForecast table
    writeWeather(weatherCSV, Resort, weatherDate, snowDepth, status)

    # Massage the StayPricing data
    #date = createDates();
    date = '2018-03-23'
    multiplier = 1.0;
    stayPrice = createStayPrice(rating, multiplier)
    bookingURL = createBooking(Resort)

    # Output data for the StayPricing table
    writePricing(stayCSV, Resort, date, stayPrice, ticketPrice, bookingURL)

    # Massage the Flight table data
    startCities = ['Chicago', 'New York', 'Washington D.C.', 'Boston', 'Los Angeles', 'Seattle']
    date = '2018-03-23'
    for city in startCities:
        trip = createFlight(Resort)
        writeFlight(flightCSV, city, Resort, date, trip)
                                                                             
def writeResort(csv, resort, price, difficulty, startSeason, endSeason, image, rating, location):
    length = len(resort)
    i = 0
    while i < length:
        fields = (resort[i],location[i][0], location[i][1], startSeason[i], endSeason[i], image[i], rating[i], difficulty[i])
        csv.writerow(fields)
        i = i + 1            

def writeWeather(csv, resort, updateDate, snowDepth, status):
    length = len(resort)
    i = 0
    while i < length:
        fields = (resort[i],updateDate[i], snowDepth[i], status[i])
        csv.writerow(fields)
        i = i + 1  

def writePricing(csv, resort, date, stayPrice, ticketPrice, bookingURL):
    length = len(resort)
    i = 0
    while i < length:
        fields = (resort[i], date, stayPrice[i], ticketPrice[i], bookingURL[i])
        csv.writerow(fields)
        i = i + 1 

def writeFlight(csv, city, resort, date, trip):
    length = len(resort)
    i = 0
    while i < length:
        fields = (city, resort[i], date, trip[i][0], trip[i][1])
        csv.writerow(fields)
        i = i + 1         
    
def manipResort(data):
    newName = []
    for name in data:
        newName.append(name[11:])
    return newName

def manipPrice(data):
    newPrice = []
    for price in data:
        output = re.findall(r'\d+',price)
        if not output:
            output = "100.0"
        newPrice.append(output[0])
    return newPrice
    
def manipDifficulty(begin, inter, advance):
    #print begin + " and " + inter + " and " + advance
    Difficulty = []
    i = 0
    length = len(begin)
    while i < length:
        beginVal = Entry2Float(begin[i])
        interVal = Entry2Float(inter[i])
        advanceVal = Entry2Float(advance[i])
        Difficulty.append((beginVal + (5.0) * interVal + (10.0) * advanceVal) / 100.0)
        i = i + 1
    return Difficulty

def manipStartSeason(season):
    
    seasonStart = []
    i = 0
    length = len(season)
    while i < length:
        if season[i] == "NULL":
            startDate = '2017-11-15'
#        endDate = '2018-04-18'
        else:
            match = re.search('\d{4}-\d{2}-\d{2}', season[i])
            startDate = match.group(0)
        seasonStart.append(startDate)
        i = i + 1
    return seasonStart

def manipEndSeason(season):
    
    seasonEnd = []
    i = 0
    length = len(season)
    while i < length:
        if season[i] == "NULL":
            endDate = '2017-11-15'
        else:
            match = re.search('\d{4}-\d{2}-\d{2}', season[i])
            endDate = match.group(0)
        seasonEnd.append(endDate)
        i = i + 1
    return seasonEnd

def manipRating(rating):
    ratingOut = []
    i = 0
    length = len(rating)
    while i < length:
        if rating[i] == 'NULL':
            rate = 3.0;
        else:
            output = re.findall(r'\d+\.\d+',rating[i])
            rate = output[0]
        ratingOut.append(rate)
        i = i + 1
    return ratingOut

def manipUpdateDate(updateDate):
    dateOut = []
    i = 0
    length = len(updateDate)
    while i < length:
        if updateDate[i] == "NULL":
            out = '2018-03-24'
        else:
            out = updateDate[i]
        i = i + 1
        dateOut.append(out)
    return dateOut

def manipSnowDepth(snowDepth):
    depthOut = []
    i = 0
    length = len(snowDepth)
    while i < length:
        if snowDepth[i] == "NULL":
            out = '30'
        else:
            out = re.findall(r'\d+',snowDepth[i])
            if not out:
                out = 30
            else:
                out = out[0]
        i = i + 1
        depthOut.append(out)
    return depthOut

def manipStatus(status):
    statusOut = []
    i = 0
    length = len(status)
    while i < length:        
        statusVal = status[i].lower()
        statusVal = re.findall(r'open',statusVal)
        if not statusVal:
            statusOut.append("Closed")
        elif status[i] == 'NULL':
            statusOut.append("Closed")
        else:
            statusOut.append("Open")
        i = i + 1
    return statusOut

# This routine will brew up the cost to stay in this location
def createStayPrice(rating, multiplier):
    stayPrice = []
    i = 0
    length = len(rating)
    while i < length:
        cost = 45.0 * float(rating[i]) * multiplier + random.randint(1,100)
        stayPrice.append(str(cost))
        i = i + 1
    return stayPrice

#def createDates():
def createBooking(Resort):
    booking = []
    i = 0
    length = len(Resort)
    while i < length:
        booking.append('www.expedia.com')
        i = i + 1
    return booking

def createLocations(Resort):
    location = []
    i = 0
    length = len(Resort)
    while i < length:
        location.append(getLocation())
        i = i + 1
    return location
         
def getLocation():
    Locations = [['Salt Lake City', 'Utah'], ['Park City', 'Utah'], ['Camden', 'Maine'], ['Jackson', 'New Hampshire'], ['Bolton', 'Vermont'],
                 ['Vernon', 'New Jersey'], ['Union Dale', 'Pennsylvania'], ['Boyne City', 'Michigan'], ['Burnsville', 'Minnesota'],
                 ['Hansen', 'Idaho']]
    length = len(Locations) - 1
    return Locations[random.randint(1,length)]
    

def createFlight(resort):
    Airlines = ["United", "American", "Delta", "Jet Blue", "SouthWest"]
    airLen = len(Airlines) - 1
    trip = []
    i = 0
    length = len(resort)
    while i < length:
        price = random.randint(251,704)
        airline = Airlines[random.randint(1,airLen)]
        trip.append([price, airline])
        i = i + 1
    return trip

def Entry2Float(str):
    paren = re.search('\(([^)]+)', str).group(1)
    output = re.findall(r'\d+',paren)
    strRes = output[0]
    return float(strRes)

if __name__ == '__main__':
    massage("resort2.info")
