<?php

namespace phpGPX;

class Config
{
    /**
     * Create Stats object for each track, segment and route
     * @var bool
     */
    public bool $calculateStats = true;

    /**
     * Additional sort based on timestamp in Routes & Tracks on XML read.
     * Disabled by default, data should be already sorted.
     * @var bool
     */
    public bool $sortByTimeStamp = false;

    /**
     * Default DateTime output format in JSON serialization.
     * @var string
     */
    public string $datetimeFormat = 'c';

    /**
     * Default timezone for display.
     * Data are always stored in UTC timezone.
     * @var string
     */
    public string $datetimeTimezone = 'UTC';

    /**
     * Pretty print.
     * @var bool
     */
    public bool $jsonPrettyPrint = true;

    /**
     * In stats elevation calculation: ignore points with an elevation of 0
     * This can happen with some GPS software adding a point with 0 elevation
     *
     * @var bool
     */
    public bool $ignoreZeroElevation = true;

    /**
     * Apply elevation gain/loss smoothing? If true, the threshold in
     * ELEVATION_SMOOTHING_THRESHOLD and ELEVATION_SMOOTHING_SPIKES_THRESHOLD (if not null) applies
     * @var bool
     */
    public bool $applyElevationSmoothing = false;

    /**
     * if APPLY_ELEVATION_SMOOTHING is true
     * the minimum elevation difference between considered points in meters
     * @var int
     */
    public int $elevationSmoothingThreshold = 2;

    /**
     * if APPLY_ELEVATION_SMOOTHING is true
     * the maximum elevation difference between considered points in meters
     * @var int|null
     */
    public ?int $elevationSmoothingSpikesThreshold = null;

    /**
     * Apply distance calculation smoothing? If true, the threshold in
     * DISTANCE_SMOOTHING_THRESHOLD applies
     * @var bool
     */
    public bool $applyDistanceSmoothing = false;

    /**
     * if APPLY_DISTANCE_SMOOTHING is true
     * the minimum distance between considered points in meters
     * @var int
     */
    public int $distanceSmoothingThreshold = 2;

}