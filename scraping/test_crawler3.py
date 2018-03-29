import re
import urlparse
import urllib2
import time
from datetime import datetime
import robotparser
import Queue
from bs4 import BeautifulSoup
import csv
#import scrape_callback2

def test_crawler(seed_url, link_class=None, delay=5, max_depth=-1, max_urls=-1, headers=None, user_agent='cs411', proxy=None, num_retries=1, scrape_callback=None):
    """Crawl from the given seed URL following links matched by link_regex
    """
    # the queue of URL's that still need to be crawled
    crawl_queue = [seed_url]
    # the URL's that have been seen and at what depth
    seen = {seed_url: 0}
    # track how many URL's have been downloaded
    num_urls = 0
    rp = get_robots(seed_url)
    throttle = Throttle(delay)
    headers = headers or {}
    if user_agent:
        headers['User-agent'] = user_agent

    while crawl_queue:
        #extract from url list
        url = crawl_queue.pop()
        # check depth of current url
        depth = seen[url]
        #print "depth of seen url: " + url + " is " + str(depth) 
        # check url passes robots.txt restrictions
        if rp.can_fetch(user_agent, url):
            throttle.wait(url)
            # download the html code
            print "downloading url = " + url
            html = download(url, headers, proxy=proxy, num_retries=num_retries)
            links = []
            # TODO:  Check and see if this can be deleted since scraping will be grabbing links beneath the main one
            #if scrape_callback:
            #    links.extend(scrape_callback(url, html) or [])

            # TODO:  Make this one the initial depth case where all the links are grabbed (i.e. test for depth == 0)
            if depth == 0:
                # can still crawl further
                if link_class:
                    # filter for links matching our regular expression
#                    links.extend(link for link in get_links(html) if re.search(link_regex, link))
                    soup = BeautifulSoup(html, 'html.parser')
#                    links.extend(link for link in get_links(soup, link_class))
                    page_links = get_links(soup, link_class)
                    #print "The page links list is "
                    #print page_links
                    for link in page_links:
                        #link = normalize(seed_url, link)
                        # check whether already crawled this link
                        if link not in seen:
                            seen[link] = depth + 1
                            # check link is within same domain
                            if same_domain(seed_url, link):
                                # success! add this new link to queue
                                crawl_queue.append(link)
                            
            # TODO:  Make this a depth == 1 case where all the subpages are scraped
            elif depth == 1:
                # download the weather report
                # Now get the weather report
                weatherURL = url + "snow-report/"
                print "Processing the snow report at: " + weatherURL
                weatherHTML = download(weatherURL, headers, proxy=proxy, num_retries=num_retries)
                if scrape_callback:
                    scrape_callback(url, html, weatherHTML)
                print "Scraping the site: " + url
                

            else:
                print "Depth exceeds 1, Sorry will not scrape the site :" + url
                
            # check whether have reached downloaded maximum
            num_urls += 1
            if num_urls == max_urls:
                break
        else:
            print 'Blocked by robots.txt:', url


class Throttle:
    """Throttle downloading by sleeping between requests to same domain
    """
    def __init__(self, delay):
        # amount of delay between downloads for each domain
        self.delay = delay
        # timestamp of when a domain was last accessed
        self.domains = {}
        
    def wait(self, url):
        """Delay if have accessed this domain recently
        """
        domain = urlparse.urlsplit(url).netloc
        last_accessed = self.domains.get(domain)
        if self.delay > 0 and last_accessed is not None:
            sleep_secs = self.delay - (datetime.now() - last_accessed).seconds
            if sleep_secs > 0:
                time.sleep(sleep_secs)
        self.domains[domain] = datetime.now()



def download(url, headers, proxy, num_retries, data=None):
    #print 'Downloading:', url
    request = urllib2.Request(url, data, headers)
    opener = urllib2.build_opener()
    if proxy:
        proxy_params = {urlparse.urlparse(url).scheme: proxy}
        opener.add_handler(urllib2.ProxyHandler(proxy_params))
    try:
        response = opener.open(request)
        html = response.read()
        code = response.code
    except urllib2.URLError as e:
        print 'Download error:', e.reason
        html = ''
        if hasattr(e, 'code'):
            code = e.code
            if num_retries > 0 and 500 <= code < 600:
                # retry 5XX HTTP errors
                html = download(url, headers, proxy, num_retries-1, data)
        else:
            code = None
    return html


def normalize(seed_url, link):
    """Normalize this URL by removing hash and adding domain
    """
    link, _ = urlparse.urldefrag(link) # remove hash to avoid duplicates
    return urlparse.urljoin(seed_url, link)


def same_domain(url1, url2):
    """Return True if both URL's belong to same domain
    """
    return urlparse.urlparse(url1).netloc == urlparse.urlparse(url2).netloc


def get_robots(url):
    """Initialize robots parser for this domain
    """
    rp = robotparser.RobotFileParser()
    rp.set_url(urlparse.urljoin(url, '/robots.txt'))
    rp.read()
    return rp
        

def get_links(soup,link_class):
    """Return a list of links from html 
    """
    # a regular expression to extract all links from the webpage
#    webpage_regex = re.compile('<a[^>]+href=["\'](.*?)["\']', re.IGNORECASE)
#    webpage_regex = re.compile('<a[^>]+href=http://www.skiresort.info["/(.*?)["/']', re.IGNORECASE)
    # TODO:  Insert a soup find here
    #soup = BeautifulSoup(html, 'html.parser')
    resortList = []
    for resort in soup.find_all('a', class_=link_class):
        resortList.append(resort.get('href'))
    # list of all links from the webpage
    return resortList

def fixUnicode(string):
    testString = '100'
    if string == None:
        return "NULL"
    asciiString = string.encode('utf-8')
    if type(asciiString) == type(testString):
        return asciiString
    else:
        return "NULL"


def findStateWithin(Str):
    stateList = ["colorado", "california", "vermont", "utah", "montana", "wyoming",
                "oregon", "new york", "maine", "idaho", "washington", "new mexico"]
    lowerStr = Str.lower()
    for state in stateList:
        stateSear = re.escape(state)
        if re.search(stateSear, lowerStr):
            return re.search(stateSear, lowerStr).group()
    return "california"
    


# TODO:  Add capability to parse weather info as well
class ScrapeCallback2:
    def __init__(self):
        self.writer = csv.writer(open('resort2.info', 'w'))
        # Adding city and state here
        self.fields = ('resortName', 'ticketPrice', 'beginner', 'inter', 'advanced', 'season', 'rating','image link', 'UpdateDate', 'SnowDepth',  'RunStatus', 'city', 'state')
#        self.fields = ('resortName', 'ticketPrice', 'beginner', 'inter', 'advanced', 'season', 'rating','image link', 'UpdateDate', 'SnowDepth',  'RunStatus')
        self.writer.writerow(self.fields)
        #self.writer = csv.writer(open('resort2.weather', 'w'))
        #self.fields = ('resortName', 'UpdateDate', 'SnowDepth',  'RunStatus')

    def __call__(self, url, html, weatherHTML):
        """ This routine extracts the data from the individual pages to be stored in a table """
        soup = BeautifulSoup(html, 'html.parser')
        resortName = soup.find(class_="fn").string
        resortName = fixUnicode(resortName)
        ticketPrice = soup.find(id="selTicketA")
        if ticketPrice == None:
            ticketPrice = "NULL";
        else:
            ticketPrice = ticketPrice.string
        ticketPrice = fixUnicode(ticketPrice)
        beginner = soup.find(id="selBeginner").string
        beginner = fixUnicode(beginner)
        intermed = soup.find(id="selInter").string
        intermed = fixUnicode(intermed)
        advanced = soup.find(id="selAdv").string
        advanced = fixUnicode(advanced)
        seasonTag = soup.find(id="selSeason")
        if seasonTag == None:
            season = soup.find(id="selGenseason")
            if season == None:
                season = "NULL"
            else:
                season = season.string
        else:
            season = seasonTag.string
        season = fixUnicode(season)
        rating = soup.find(id="selRating").string
        imageDiv = soup.find(class_="col-md-8")
        imageTag = imageDiv.find("img")
        imageSrc = imageTag['src']
        imageSrc = fixUnicode(imageSrc)
        imageSrc = "http://www.skiresort.info/" + imageSrc

        # Handle the weather report
        Weather = BeautifulSoup(weatherHTML, 'html.parser')
        # Get the date of update
        weatherSect = Weather.find(class_=("col-md-8"))
        weatherFine = weatherSect.find(class_=("detail-links"))
        weatherFinest = weatherFine.find(class_=("description"))
        if weatherFinest != None:
            updateDate = weatherFinest.string
        else:
            updateDate = "NULL"
        updateDate = fixUnicode(updateDate)
        
        # Get the current status
        openStatus = weatherSect.find(class_="open")
        if openStatus != None:
            openStatus = fixUnicode(openStatus.string)
        else:
            openStatus = "NULL"
        
        # Get the snow depth
        snowDepth = weatherSect.tbody.td
        if snowDepth != None:        
            snowDepth = fixUnicode(snowDepth.string)
        else:
            snowDepth = "NULL"
        
        # Get the city results
        book = soup.find(id="bs")
        city = book.option.string
        print "The city found is " + city

        # Get the state results
        #state = 'state here'
        text = soup.find(class_="panel-simple")
        statePara = text.p.a
        state = findStateWithin(statePara.string)

        # Write the final results
        results = (resortName, ticketPrice, beginner, intermed, advanced, season, rating,
                   imageSrc, updateDate, snowDepth, openStatus, city, state)
        self.writer.writerow(results)       
               

if __name__ == '__main__':
#    test_crawler('http://www.skiresort.info/ski-resorts/usa/page/2/', 'pull-right btn btn-default btn-sm', delay=4, num_retries=1, max_depth=1, scrape_callback=ScrapeCallback2())
    test_crawler('http://www.skiresort.info/ski-resorts/usa/', 'pull-right btn btn-default btn-sm', delay=4, num_retries=1, max_depth=1, scrape_callback=ScrapeCallback2())

