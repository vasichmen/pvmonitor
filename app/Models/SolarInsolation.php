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
    ];

    protected $primaryKey = ['lat', 'lon'];
}
