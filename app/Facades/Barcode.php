<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 24.05.2019
 * Time: 15:13
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;


class Barcode extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'Barcode';
    }
}