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
    ];
}
