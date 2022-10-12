<?php


namespace App\Models;

class SolarInsolation extends AbstractModel
{
    protected static bool $fillsCode = true;

    protected $fillable = [
        'name',
        'code',
    ];
}
