import requests
import csv

resort2 = 'scraping/resort2.info'
resort_reader = csv.reader(open(resort2, 'r', encoding='utf-8'))
i = 1

for row in resort_reader:
    if i > 1:
        image_url = row[7]
        resort_name = row[0]

        with open(resort_name + '.jpg', 'wb') as f:
            f.write(requests.get(image_url).content)

    i += 1

