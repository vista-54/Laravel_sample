<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClientExport implements WithMultipleSheets
{
    use Exportable;



    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ClientModelExport();
//        $sheets[] = new ClientLogExport($this->client);

        return $sheets;
    }
}
