<?php

namespace phpGPX\Models;

enum PointType: string
{
	case Waypoint = 'wpt';
	case Trackpoint = 'trkpt';
	case Routepoint = 'rtept';
}
