<?php

namespace App\Exports;

use App\Exports\Model\CouponModel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CouponExport implements WithMultipleSheets
{
    use Exportable;



    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new CouponModel();
//        $sheets[] = new ClientLogExport($this->client);

        return $sheets;
    }
}
