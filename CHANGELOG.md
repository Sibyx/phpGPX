# Changelog

## 1.2.1 : 2022-07-30

- **Fixed**: Fixed `VERSION` string in `phpGPX.php`

## 1.2.0 : 2022-07-30

- **Changed**: [Real distance calculation #37](https://github.com/Sibyx/phpGPX/issues/37) (DistanceCalculator refactor)

## 1.1.3 : 2021-07-29

- **Fixed**: [Fix negative duration #58](https://github.com/Sibyx/phpGPX/pull/58) by [@neronmoon](https://github.com/neronmoon)

## 1.1.2 : 2021-02-28

- **Fixed**: [do SORT_BY_TIMESTAMP only for tracks with timestamps #52](https://github.com/Sibyx/phpGPX/pull/52)

## 1.1.1 : 2021-02-15

- **Fixed**: Fixed `VERSION` string in `phpGPX.php`

## 1.1.0 : 2021-02-05

- **Added**: [Limiting maximum elevation difference to protect from spikes](https://github.com/Sibyx/phpGPX/pull/49) 

## 1.0.1

- **Fixed**: Fixed PersonParser::toXML() if there are no links provided 
  [Error when $person->links is null #48](https://github.com/Sibyx/phpGPX/issues/48)

## 1.0

I am not very proud of idea having a major release in such terrible state. This release is just freeze from 2017 
compatible API and behaviour with some bugfixies. It looks like some people use the library and I want to perform some
radical refactoring. See you in `2.x`. 

- **Fixed**: Do not return extra `:` while parsing unsupported extensions if there is no namespace for child element
- **Fixed**: Fixed Copyright test

## 1.0-RC5

- **Changed:** Moved PHPUnit to development dependencies. 

## 1.0-RC4

 - **Changed:** [Change the way to deal with extensions ](https://github.com/Sibyx/phpGPX/pull/19) 
 - **Fixed:** [RoutePoints and TripExtensions WIP](https://github.com/Sibyx/phpGPX/issues/22)
 - **Fixed:** [Route point rtep versus rtept](https://github.com/Sibyx/phpGPX/issues/21)
 - **Fixed:** [Empty array on load route](https://github.com/Sibyx/phpGPX/issues/20)
 - **Fixed:** Do not load zero altitude in statistics as NULL

## 1.0-RC3

 - **Added:** [Cumulative Elevation in stats](https://github.com/Sibyx/phpGPX/pull/12) with pull request #12 by @Shaydu
 - **Fixed:** [Fix for unterminated entity references](https://github.com/Sibyx/phpGPX/pull/13) with #13 by @benlumley 
 - **Fixed:** [split loading and parsing in separate methods so a string may be loaded as gpx data](https://github.com/Sibyx/phpGPX/pull/9) with #9 by @lommes 
 - **Fixed:** HeartRate [typo that lead to error](https://github.com/Sibyx/phpGPX/issues/14)
 - **Fixed:** Skipping RC2 in packagist [Missing version in packagist](https://github.com/Sibyx/phpGPX/issues/10) 

## 1.0-RC2

 - **Fixed:** [waypoints not loaded correctly - they are ignored](https://github.com/Sibyx/phpGPX/issues/6)
 - Init of unit tests

## 1.0-RC1

Initial release
