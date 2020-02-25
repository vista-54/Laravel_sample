<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 20.06.2019
 * Time: 13:29
 */

namespace App\Services;


class Identifier
{
    const ID_LENGTH = 10;
    const LOUALTY = 'L';
    const COUPON = 'C';
    const OFFER = 'O';

    public function generate($prefix, $id, $other_id)
    {
        return implode('-', [$prefix, $this->convert($id), $this->convert($other_id)]);
    }

    protected function convert($id)
    {
        $length = self::ID_LENGTH - strlen($id);

        return implode('', array_fill(0, $length, 0)) . $id;
    }
}
