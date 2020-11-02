from fastapi import FastAPI
import json
from fastapi.middleware.cors import CORSMiddleware
import os

thispath = os.getcwd().split('src')[0]
thispath += 'data/'

STATUSPATH = thispath+'data.json'
ZIPPATH = thispath+'zip.json'


def loadJSON(myPath):
    with open(myPath, 'r', encoding='utf-8') as jfile:
        myData = json.load(jfile)
    return myData


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


origins = [
    "*",
]

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["GET"],
    allow_headers=["*"],
)


@app.get("/")
async def root():
    return {"message": "Hello World"}


@app.get("/v1/counties")
async def list_counties():
    myData = loadJSON(STATUSPATH)
    return list(myData.keys())


@app.get("/v1/counties/{county_name}")
async def read_county(county_name):
    myData = loadJSON(STATUSPATH)
    return myData[county_name]


@app.get("/v1/zip")
async def list_zipcodes():
    myData = loadJSON(ZIPPATH)
    return list(myData.keys())


@app.get("/v1/zip/{zip_code}")
async def read_zipcode(zip_code):
    zipData = loadJSON(ZIPPATH)
    myData = loadJSON(STATUSPATH)
    res = zipToCounty(zip_code, zipData)
    res = myData[res]
    temp = {'name': res['name'],
            'full_name': res['full_name'],
            'level': res['level'],
            'full_level': res['full_level'],
            'color': res['color']}
    return temp
