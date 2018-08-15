# SurfNow

- [**Purpose**](#purpose)
- [**Algorithm**](#algorithm)
- [**Data Sources**](#data-sources)
    - [NOAA](#noaa)
    - [Surfline](#surfline)
        - [New API](#new-api)
            - [Taxonomy ](#taxonomy)
            - [KBYG (Know Before You Go) ](#kbyg)
        - [Legacy API](#legacy-api)
            - [Forecasts](#forecasts)
            - [Mobile](#mobile)

## Purpose

To tell where to surf NOW

## Algorithm

tbd

## Data Sources

### NOAA

[NOAA.gov](noaa.gov)


### SURFLINE

It seems [Surfline](surfline.com) recently did a sleek and modern redesign of its website. My guess is that, in the process, they also overhauled their API. But instead of calling the new API `v2`, they changed the base url. The new API is still undocumented, but I'd say it returns cleaner JSON documents for developers to use and understand. Here's how to access both APIs:

#### New API
The New API uses the following request structure: `http://services.surfline.com/{type}?{params}`

###### Taxonomy

Available querystring params:

Param|Values|Effect
-----|------|------
type|string|Possible values are: `taxonomy`,`geoname`,`region`,`subregion`, and `spot`.
id|string|Surfline's hashed ID based on the type. E.g. if you picked `spot`, supply the spot ID.
maxDepth|integer|Depth of the returned taxonomy JSON

Example calls:
```
Earth: http://services.surfline.com/taxonomy?type=taxonomy&id=58f7ed51dadb30820bb38782&maxDepth=0

South Los Angeles: http://services.surfline.com/taxonomy?type=subregion&id=58581a836630e24c4487900b

El Porto Beach: http://services.surfline.com/taxonomy?type=spot&id=5842041f4e65fad6a7708906
```
###### KBYG

KBYG Subregional Overview:
`http://services.surfline.com/kbyg/regions/overview?subregionId=58581a836630e24c4487900b`

KBYG Subregional Forecasts:
`http://services.surfline.com/kbyg/regions/forecasts?subregionId=58581a836630e24c4487900b`

KBYG Regional Analyses:
`http://services.surfline.com/feed/regional?subregionId=58581a836630e24c4487900b`

KBYG Mapview Spots:
`http://services.surfline.com/kbyg/mapview?south=33.667782574792184&west=-118.56994628906251&north=34.028762179464465&east=-118.04054260253908`

KBYG Spot Forecasts:
`http://services.surfline.com/kbyg/spots/forecasts?spotId=5842041f4e65fad6a7708906&days=1`

KBYG Conditions:
`http://services.surfline.com/kbyg/spots/forecasts/conditions?spotId=5842041f4e65fad6a7708906&days=6`

KBYG Tides:
`http://services.surfline.com/kbyg/spots/forecasts/tides?spotId=5842041f4e65fad6a7708906&days=1`

KBYG Waves:
`http://services.surfline.com/kbyg/spots/forecasts/wave?spotId=5842041f4e65fad6a7708906&days=1&intervalHours=1`

KBYG Weather:
`http://services.surfline.com/kbyg/spots/forecasts/weather?spotId=5842041f4e65fad6a7708906&days=1&intervalHours=1`

KBYG Wind:
`http://services.surfline.com/kbyg/spots/forecasts/wind?spotId=5842041f4e65fad6a7708906&days=1&intervalHours=1`


#### Legacy API
The legacy API uses the following request structure: `http://api.surfline.com/v1/{type}/{spot_id}?{params}`

###### Forecasts

Available querystring params:

Param|Values|Effect
-----|------|------
spot_id|integer|Surfline spot id.
resources|string|Any combination of "analysis,confidence,hireswind,hvp,quickspot,sort,surf,surflineweather,tide,ureport,watertemp,weather,wind". "Sort" gives an array of swells, periods & heights that are used for the forecast tables on spot forecast pages [e.g. El Porto](https://www.surfline.com/surf-report/el-porto/5842041f4e65fad6a7708906/forecast). (When resource is set to `all`, you get `analysisconfidencehvpsortsurftidewatertempweatherwind`. Or, when unspecified, Default: `analysischartconfidencehireswindhvpquickspotsortsurfsurflineweathertideureportwatertempweatherwind`)
days|integer|Number of days of forecast to get. This seems to cap out at 16 for Wind and 25 for Surf. (Credit source) (Default: `17`)
units|string|`e` uses American units (ft/mi), `m` uses metric (Default: `e`)
showOptimal|boolean|Includes arrays of doubles (0-1) indicating whether each wind & swell forecast is optimal for this spot or not. Included in the Sort and Wind JSON object. (Default: `false`)
interpolate|boolean|`true` interpolates "forecasts" into every 3 hours instead of the default every 6. (Default: `false`)
fullAnalysis|boolean|`true` adds--if exists--fields like `brief_outlook`, `best_bet`, and `extended_outlook` to the Analysis JSON object. Doesn't work for all queries. (Default: `false`)
usenearshore|boolean|Set to `true` to use the [more accurate nearshore models](http://www.surfline.com/surf-science/what-is-lola---forecaster-blog_61031/) that take into account how each spot's unique bathymetry affects the incoming swells. (Credit source) (Deafult: `false`)
aggregate|boolean|`true` enables aggregate fields for the Surf resource. Doesn't work for all queries. (Default: `false`)
getAllSpots|boolean|`true` returns an array of data for all spots in the same region as `spot_id` (Default: `false`)
callback|string|Callback function name

There seems to be three more parameters that I have yet to discover. One is boolean, one is integer, and one is string/character. All I know is their default values are `false`, `1`, and `e`, respectively.

###### Mobile
Example API calls found by snooping around:
```
https://api.surfline.com/v1/mobile/report/4900

https://api.surfline.com/v1/mobile/nearby/4900?resources=buoy&unit=FT
```
