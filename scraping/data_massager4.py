import csv
import re
import random
import datetime

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
    city = []
    state = []
    
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
        city.append(row[11])
        state.append(row[12])

    # Massage data for Resort table
    Resort = manipResort(Resort)
    ticketPrice = manipPrice(ticketPrice)
    difficulty = manipDifficulty(beginner, intermed, advanced)
    startSeason = manipStartSeason(season)
    endSeason = manipEndSeason(season)
    rating = manipRating(rating)
    city = manipCity(city)
    state = manipState(state)

    # Output data for the Resort table
    writeResort(resortCSV, Resort, ticketPrice, difficulty, startSeason, endSeason, image, rating, city, state)

    # Massage the weather forecast data
    weatherDate = manipUpdateDate(updateDate)
    snowDepth = manipSnowDepth(snowDepth)
    status = manipStatus(status)

    # Output data for the WeatherForecast table
    startDate = datetime.date(2017,10,25)
    endDate = datetime.date(2018,04,25)
    stepAmount = datetime.timedelta(7)
    dateExtent = getDateExtent(startDate, endDate, stepAmount)
    # parameter for weather model
#    apexDate = datetime.date(2018,01,20)
#    headDate = datetime.date(2017,10,21)
#    tailDate = datetime.date(2018,04,15)
#    minSnow = 30.0;
#    maxSnow = 500.0;
    # Convert weatherDate to datetime format
    weatherDateConv = ConvDate(weatherDate)

    # loop over dateExtent and compute estimated weather conditions based on acquired value
    for computedDate in dateExtent:
        # Compute the snowDepth for a given date
        newSnowDepth = ComputeDepth2(len(Resort), computedDate, weatherDateConv, snowDepth)
        newStatus = ComputeStatus(status, computedDate, weatherDateConv)
        computedDate = ComputeNewDate(len(Resort), computedDate)
        writeWeather(weatherCSV, Resort, computedDate, newSnowDepth, newStatus)

    # Massage the StayPricing data
    #date = createDates()
    date = '2018-03-23'
    multiplier = 1.0;
    stayPrice = createStayPrice(rating, multiplier)
    bookingURL = createBooking(Resort)
    # Convert weatherDate to datetime format
    actDate = replDate(len(Resort), date)
    actDateConv = ConvDate(actDate)

    # Output data for the StayPricing table
    for computedDate in dateExtent:
        newStayPrice = ComputePrice(len(Resort), computedDate, actDateConv, stayPrice)
        newTicketPrice = ComputePrice(len(Resort), computedDate, actDateConv, ticketPrice)
        writePricing(stayCSV, Resort, str(computedDate), newStayPrice, newTicketPrice, bookingURL)

    # Massage the Flight table data
    startCities = ['Chicago', 'New York', 'Washington D.C.', 'Boston', 'Los Angeles', 'Seattle']
    date = '2018-03-23'
    for city in startCities:
        trip = createFlight(Resort)
        writeFlight(flightCSV, city, Resort, date, trip)
                                                                             
def writeResort(csv, resort, price, difficulty, startSeason, endSeason, image, rating, city, state):
    length = len(resort)
    i = 0
    while i < length:
#        fields = (resort[i],location[i][0], location[i][1], startSeason[i], endSeason[i], image[i], rating[i], difficulty[i])
        fields = (resort[i],city[i], state[i], startSeason[i], endSeason[i], image[i], rating[i], difficulty[i])
        csv.writerow(fields)
        i = i + 1            

def writeWeather(csv, resort, updateDate, snowDepth, status):
    length = len(resort)
    i = 0
    while i < length:
        fields = (resort[i],str(updateDate[i]), snowDepth[i], status[i])
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
            end = season[i]
            end = end[10:]
            match = re.search('\d{4}-\d{2}-\d{2}', end)
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
    
# Create a dummy routine until know more about the structure of the data
def manipCity(city):
    return city

# Create a dummy routine until know more about the structure of the data
def manipState(state):
    return state


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

# This routine computes a list of datetime objects for the timeframe to compute the weather
def getDateExtent(startDate, endDate, stepAmount):
    if startDate > endDate:
        return None
    currentDate = startDate
    dateList = [startDate]
    while currentDate < endDate:
        currentDate = currentDate + stepAmount
        dateList.append(currentDate)
    return dateList

#def ComputeDepth(numResorts, computedDate, weatherDate, actSnowDepth, apexDate, headDate, tailDate, minSnow, maxSnow):

#    newSnowDepth = []
#    index = 0
#    dayDiff = computedDate - apexDate
#    #print "The day diff is " + str(abs(dayDiff))
#    dayDiffFloat = float(re.search(r'[0-9]+',str(dayDiff)).group())
#    if computedDate > apexDate:
#        baseDiff = tailDate - apexDate
#        baseDiffFloat = float(re.search(r'[0-9]+',str(baseDiff)).group())
#    else:
#        baseDiff = apexDate - headDate
#        baseDiffFloat = float(re.search(r'[0-9]+',str(baseDiff)).group()) 
    #print "The base diff float is " + str(baseDiffFloat)
#    inputVal = dayDiffFloat / baseDiffFloat
    #print "The input val is " + str(inputVal)
#    outputVal = weatherFunc(inputVal)
    #print "The output val is " + str(outputVal)
#    while(index < numResorts):
#        apexSnow = float(actSnowDepth[index]) / outputVal
#        compDiff = abs(weatherDate[index] - apexDate)
#        compDiffFloat = float(re.search(r'[0-9]+',str(compDiff)).group())
#        snowAmount = apexSnow * (1.0 - (compDiffFloat / baseDiffFloat))
#        if snowAmount > maxSnow:
#            snowAmount = maxSnow
#        if snowAmount < minSnow:
#            snowAmount = minSnow
#        newSnowDepth.append(snowAmount)
#        index = index + 1
#    return newSnowDepth

def ComputeDepth2(numResorts, computedDate, weatherDate, actSnowDepth):
    newSnowDepth = []
    index = 0
    threshold = datetime.timedelta(5)
    while(index < numResorts):
        if threshold > abs(computedDate - weatherDate[index]):
            newSnowDepth.append(actSnowDepth[index])
        else:
            snowAmt = float(actSnowDepth[index])
            snowDepthPercent = 0.20 * snowAmt
            snowDepthVar = random.randint(1,int(snowDepthPercent))
            snowDepthVar = snowDepthVar - (0.5) * snowDepthPercent
            newSnowDepth.append(snowAmt + snowDepthVar)
        index = index + 1
    return newSnowDepth

def ComputePrice(numResorts, computedDate, actDate, actPrice):
    newPrice = []
    index = 0
    threshold = datetime.timedelta(5)
    while(index < numResorts):
        if threshold > abs(computedDate - actDate[index]):
            newPrice.append(actPrice[index])
        else:
            priceAmt = float(actPrice[index])
            pricePercent = 0.40 * priceAmt
            pricePerInt = int(pricePercent)
            if pricePerInt < 2:
                pricePerInt = 10
            #print pricePerInt
            priceVar = random.randint(1,pricePerInt)
            priceVar = priceVar - (0.5) * pricePercent
            newPrice.append(priceAmt + priceVar)
        index = index + 1
    return newPrice


def ComputeNewDate(length, computedDate):
    newDateList = []
    index  = 0
    while index < length:
        newDateList.append(computedDate)
        index = index + 1
    return newDateList
            
def weatherFunc(inputVal):
    
    xVals = [0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0]
    yVals = [1.0, 0.98, 0.95, 0.91, 0.88, 0.84, 0.80, 0.75, 0.65, 0.55, 0.1]
    i = 0
    while inputVal > xVals[i]:
        i = i + 1
        # print i
        if i == 10:
            break
    if i == 0:
        return 1.0
    elif i == 10:
        return 0.1
    else:
        return  yVals[i-1] - (yVals[i-1] - yVals[i])/(xVals[i-1] - xVals[i]) * (inputVal - xVals[i-1])

def ConvDate(weatherDate):
    convertDate = []
    for wdate in weatherDate:
        convDate = re.findall(r'[0-9]+',wdate)
        convertDate.append(datetime.date(int(convDate[0]),int(convDate[1]),int(convDate[2])))
    return convertDate
     
def replDate(length, date):
    outputDate = []
    index = 0
    while index < length:
        outputDate.append(date)
        index = index + 1
    return outputDate
   
# TODO:  Update this function later
def ComputeStatus(status, computedDate, weatherDate):
    newStatus = []
    index = 0
    threshold = datetime.timedelta(5)
    for state in status:        
        if threshold > abs(computedDate - weatherDate[index]):
            newStatus.append(state)
        else:
            newStatus.append("Open")
        index = index + 1
    return newStatus

def Entry2Float(str):
    paren = re.search('\(([^)]+)', str).group(1)
    output = re.findall(r'\d+',paren)
    strRes = output[0]
    return float(strRes)

if __name__ == '__main__':
    massage("resort2.info")
