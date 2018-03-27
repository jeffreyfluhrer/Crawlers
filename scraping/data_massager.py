import csv
import re

# This script will produce three files that will be the csv inputs for the Resort, StayPricing and
# WeatherForecast tables.
# Resort schema is Resort(ResortName, City, State, SeasonStartDAte, SeasonEndDate, ImageuRL, rating, difficulty)
# 

def massage(file1):
    reader1 = csv.reader(open(file1, 'r'))
    resortCSV = csv.writer(open('resort.out','w'))
    stayCSV = csv.writer(open('staypricing.out','w'))
    weatherCSV = csv.writer(open('weatherforecast.out','w'))

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

    Resort = manipResort(Resort)
    ticketPrice = manipPrice(ticketPrice)
    difficulty = manipDifficulty(beginner, intermed, advanced)
    startSeason = manipStartSeason(season)
    endSeason = manipEndSeason(season)
    writeResort(resortCSV, Resort, ticketPrice, difficulty, startSeason, endSeason)
                                                                             
def writeResort(csv, resort, price, difficulty, startSeason, endSeason):
    length = len(resort)
    i = 0
    while i < length:
        fields = (resort[i],"city", "state", startSeason[i], endSeason[i], price[i], difficulty[i])
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
            output = "NULL"
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
#        endDate = '2018-04-18'
        else:
            match = re.search('\d{4}-\d{2}-\d{2}', season[i])
            endDate = match.group(0)
        seasonEnd.append(endDate)
        i = i + 1
    return seasonEnd

def Entry2Float(str):
    paren = re.search('\(([^)]+)', str).group(1)
    output = re.findall(r'\d+',paren)
    strRes = output[0]
    return float(strRes)

if __name__ == '__main__':
    massage("resort2.info")
