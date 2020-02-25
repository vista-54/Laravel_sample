<?php

namespace App\Exports;

use App\Http\Resources\Admin\Export\ClientExportResource;
use App\Http\Resources\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientModelExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Joined date',
            'Joined Location',
            'Unique ID',
            'IOS/Android',
            'Email',
            'First Name',
            'Last Name',
            'Telephone',
            'Country Code',
            'Birthday',
            'Timezone',
            'Address',
            'Age',
            'Race',
            'Points',
            'Total amount spent',
            'Total number of transactions',
            'Total number of offer used',
            'Total number of coupons redeemed',
            'Login Type (FB, Email)'
        ];
    }

    public function collection()
    {
        return new Collection(ClientExportResource::collection(auth()->user()->clients()->get()));
    }

}
