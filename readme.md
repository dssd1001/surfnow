# SurfNow

## Purpose

To tell where to surf NOW

## Data Sources

#### NOAA

[NOAA.gov](noaa.gov)

#### SURFLINE
Request structure: `http://api.surfline.com/v1/forecasts/<spot_id>?params`

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
