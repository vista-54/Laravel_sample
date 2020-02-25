<?php

namespace App\Exports;

use App\Exports\Model\LoyaltyProgramModel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LoyaltyProgramExport implements WithMultipleSheets
{
    use Exportable;



    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new LoyaltyProgramModel();
//        $sheets[] = new ClientLogExport($this->client);

        return $sheets;
    }
}
