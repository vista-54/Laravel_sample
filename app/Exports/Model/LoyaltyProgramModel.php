<?php

namespace App\Exports\Model;

use App\Http\Resources\Admin\Export\LoyaltyProgramExportResource;
use App\Http\Resources\Collection;
use App\Models\Client;
use App\Models\ClientOffer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LoyaltyProgramModel implements FromCollection, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Date Time',
            'Coupon ID',
            'Client first name',
            'Client last name',
            'Offer Value points',
            'points per offer',
            'currency',
            'Redeemed amount',
            'Redeemed location',
            'Staff name'
        ];
    }

    public function collection()
    {
        $clients = auth()->user()->clients()->pluck('id');
        return new Collection(LoyaltyProgramExportResource::collection(ClientOffer::whereIn('client_id', $clients)->get()));
    }

}
