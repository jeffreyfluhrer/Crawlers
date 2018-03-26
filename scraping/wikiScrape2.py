import urllib2
from bs4 import BeautifulSoup
import csv

def scrape(html):
    
    # Open and initialize CSV file
    writer = csv.writer(open('resort3.info', 'w'))
    fields = ('resortName', 'city', 'state')
    writer.writerow(fields)
    
    # Create soup
    soup = BeautifulSoup(html, 'html.parser')
    
    # Get the list of states
    states = soup.find_all("h3")
    
    # Now iterate over list
    for state in states:
            stateName = state.find("a")
            if stateName == None:
                break
            stateName = stateName.string
            stateName.encode('utf-8')
            print "The current state name is: " + stateName
            # Now extract list of resort names and city locations
            resorts = state.next_sibling.next_sibling
            # Now iterate of resort list for a given state
            for resortList in resorts.find_all("li"):               
                currentResort = resortList.find_all("a")
                if len(currentResort) != 2:
                    continue
                # split the city and resort name and add to CSV table
                if not currentResort:
                    continue
                resortName = currentResort[0]
                if not resortName:
                    continue
                if 'title' not in resortName:
                    resortName = resortName.string
                else:   
                    resortName = resortName.attrs["title"]
                resortName.encode('utf-8')
                print "The current resort name is: " + resortName
                cityName = currentResort[1].attrs["title"]
                cityName.encode('utf-8')
                print "The current city name is: " + cityName
                fields = (resortName, cityName, stateName)
                writer.writerow(fields)

             
    
               

if __name__ == '__main__':
    html = urllib2.urlopen('https://en.wikipedia.org/wiki/List_of_ski_areas_and_resorts_in_the_United_States')
    scrape(html)
