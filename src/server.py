from fastapi import FastAPI
import json
from fastapi.middleware.cors import CORSMiddleware
import os

thispath = os.getcwd().split('src')[0]
thispath += 'data/'

HEROKU_STATUS_PATH = os.environ['HEROKU_STATUS_PATH']
HEROKU_ZIP_PATH = os.environ['HEROKU_ZIP_PATH']


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
    myData = loadJSON(HEROKU_STATUS_PATH)
    return {'counties': list(myData.keys())}


@app.get("/v1/counties/{county_name}")
async def read_county(county_name):
    myData = loadJSON(HEROKU_STATUS_PATH)
    return myData[county_name]


@app.get("/v1/zip")
async def list_zipcodes():
    myData = loadJSON(HEROKU_ZIP_PATH)
    return {'zip_codes': list(myData.keys())}


@app.get("/v1/zip/{zip_code}")
async def read_zipcode(zip_code):
    zipData = loadJSON(HEROKU_ZIP_PATH)
    myData = loadJSON(HEROKU_STATUS_PATH)
    res = zipToCounty(zip_code, zipData)
    res = myData[res]
    return {'name': res['name'],
            'full_name': res['full_name'],
            'level': res['level'],
            'full_level': res['full_level'],
            'color': res['color']}
