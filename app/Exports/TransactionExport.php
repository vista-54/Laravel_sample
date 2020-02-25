<?php

namespace App\Exports;

use App\Exports\Model\TransactionModel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TransactionExport implements WithMultipleSheets
{
    use Exportable;



    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new TransactionModel();
//        $sheets[] = new ClientLogExport($this->client);

        return $sheets;
    }
}
