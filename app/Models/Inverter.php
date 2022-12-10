<?php


namespace App\Models;

class Inverter extends AbstractModel
{
    protected $fillable = [
        'name',
        'vac',
        'pso',
        'paco',
        'pdco',
        'vdco',
        'c0',
        'c1',
        'c2',
        'c3',
        'pnt',
        'vdcmax',
        'idcmax',
        'mppt_low',
        'mppt_high',
        'cec_date',
        'cec_hybrid',
    ];


}
