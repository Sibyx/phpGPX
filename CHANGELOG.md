# Changelog

## 1.0-RC5

- **Change:** Moved PHPUnit to development dependencies. 

## 1.0-RC4

 - **Change:** [Change the way to deal with extensions ](https://github.com/Sibyx/phpGPX/pull/19) 
 - **Bugfix:** [RoutePoints and TripExtensions WIP](https://github.com/Sibyx/phpGPX/issues/22)
 - **Bugfix:** [Route point rtep versus rtept](https://github.com/Sibyx/phpGPX/issues/21)
 - **Bugfix:** [Empty array on load route](https://github.com/Sibyx/phpGPX/issues/20)
 - **Bugfix:** Do not load zero altitude in statistics as NULL

## 1.0-RC3

 - **Feature:** [Cumulative Elevation in stats](https://github.com/Sibyx/phpGPX/pull/12) with pull request #12 by @Shaydu
 - **Bugfix:** [Fix for unterminated entity references](https://github.com/Sibyx/phpGPX/pull/13) with #13 by @benlumley 
 - **Bugfix:** [split loading and parsing in separate methods so a string may be loaded as gpx data](https://github.com/Sibyx/phpGPX/pull/9) with #9 by @lommes 
 - **Bugfix:** HeartRate [typo that lead to error](https://github.com/Sibyx/phpGPX/issues/14)
 - **Bugfix:** Skipping RC2 in packagist [Missing version in packagist](https://github.com/Sibyx/phpGPX/issues/10) 

## 1.0-RC2

 - **Bugfix:** [waypoints not loaded correctly - they are ignored](https://github.com/Sibyx/phpGPX/issues/6)
 - Init of unit tests

## 1.0-RC1

Initial release