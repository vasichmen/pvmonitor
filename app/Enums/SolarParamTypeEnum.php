<?php


namespace App\Enums;

enum SolarParamTypeEnum: string
{
    case Full = 'full';
    case Direct = 'direct';
    case Diffuse = 'diffuse';
}
