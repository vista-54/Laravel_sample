<?php


namespace App\Services;


class Number
{
    public function format($number)
    {
        return number_format($number, 2, '.', ',');
    }
}
