<?php

namespace App\Exports;

use App\Http\Resources\Admin\Client\ClientLogExportResource;
use App\Http\Resources\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class ClientLogExport implements FromCollection, WithHeadings
{
    use Exportable;

    public $client;

    public function __construct($client)
    {
        $this->client = $client;
    }


    public function headings(): array
    {
        return [
            ['id', 'message', 'date'],
        ];
    }

    public function collection()
    {
        return new Collection(ClientLogExportResource::collection($this->client->logs));
    }

}