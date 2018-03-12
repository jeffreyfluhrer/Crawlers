import urllib2
from bs4 import BeautifulSoup
import csv

def scrape(html):
    soup = BeautifulSoup(html, 'html.parser')
    resort = []
    city = []
    state = []
    price = []
    table = soup.find("table", { "class" : "wikitable sortable" })
    for row in table.findAll("tr"):
        cells = row.findAll("td")
        #For each "tr", assign each "td" to a variable.
        if len(cells) == 12:
            resort.extend([cells[0].find(text=True)])
            city.extend([cells[1].find(text=True)])
            state.extend([cells[2].find(text=True)])
            price.extend([cells[10].find(text=True)])
    writer = csv.writer(open('resort.info', 'w'))
    fields = ('resortName', 'city', 'state', 'price')
    writer.writerow(fields)
    for i in range(len(resort)):
        if(state[i] != "Quebec" and state[i] != "British Columbia"):
            writer.writerow([resort[i], city[i], state[i], price[i]])    

if __name__ == '__main__':
    html = urllib2.urlopen('https://en.wikipedia.org/wiki/Comparison_of_North_American_ski_resorts')
    scrape(html)
