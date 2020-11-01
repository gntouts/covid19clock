import requests
from bs4 import BeautifulSoup
import json
from time import sleep

URL = 'https://covid19.gov.gr/covid-map/'
STATUSPATH = 'data.json'
ZIPPATH = 'zip.json'


def getScriptSrc(url):
    res = requests.get(url)
    soup = BeautifulSoup(res.content, "html.parser")
    scripts = soup.find_all('script')
    good = []
    for each in scripts:
        try:
            if 'covid19' in each.get('src'):
                good.append(each.get('src'))
            # print(each.get('src'))
        except:
            pass
            # print(each)

            # print(scripts)
    scripts = []
    for each in good:
        if 'autoptimize' in each:
            scripts.append(each)
    if len(scripts) == 1:
        return scripts[0]
    else:
        return False


def getDict(url):
    newReponse = requests.get(url).content
    newReponse = newReponse.decode('utf-8')
    temp = newReponse.split('data_namekey')[1]
    temp = temp.split(';')[0][1:]
    return temp


def main():
    script = getScriptSrc(URL)
    if script:
        data = getDict(script)
        sleep(1)
        forJson = json.loads(data)
        for each in forJson:
            this = forJson[each]
            this['name'] = each
            this['full_name'] = this.pop('name1')
            if this['color'] == 'red':
                this['full_level'] = 'Επίπεδο Β. Αυξημένου Κινδύνου'
                this['level'] = 2
            else:
                this['full_level'] = 'Επίπεδο Α. Επιτήρησης'
                this['level'] = 1
        with open(STATUSPATH, 'w', encoding='utf-8') as jfile:
            json.dump(forJson, jfile)

        with open(STATUSPATH, 'r', encoding='utf-8') as jfile:
            myData = json.load(jfile)

        zip = {}
        for each in myData:
            this = myData[each]
            codes = this['zip']
            for every in codes:
                zip[every] = each

        with open(ZIPPATH, 'w', encoding='utf-8') as jfile:
            json.dump(zip, jfile)


def zipToCounty(zip, zipDict):
    tk = zip
    tk2 = tk[:2]
    tk3 = tk[:3]
    zipCodes = list(zipDict.keys())
    if tk in zipCodes:
        return zipDict[tk]
    elif tk3 in zipCodes:
        return zipDict[tk3]
    elif tk2 in zipCodes:
        return zipDict[tk2]
    else:
        return False


def main2():
    with open(ZIPPATH, 'r', encoding='utf-8') as jfile:
        zip = json.load(jfile)

    nomos = zipToCounty('26223', zip)
    print(nomos)


if __name__ == "__main__":
    main()
