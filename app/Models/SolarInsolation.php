<?php


namespace App\Models;

class SolarInsolation extends AbstractModel
{
    protected $fillable = [
        'lat',
        'lon',
        'full',
        'direct',
        'diffuse',
        'altitude',
        'full_optimal',
        'winter_solstice',
        'summer_solstice',
        'horizon_profile',
        'random',
    ];

    protected $casts = [
        'winter_solstice' => 'array',
        'summer_solstice' => 'array',
        'horizon_profile' => 'array',
    ];

    protected $primaryKey = ['lat', 'lon'];
}
