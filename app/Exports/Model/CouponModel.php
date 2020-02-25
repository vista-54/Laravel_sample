<?php

namespace App\Exports\Model;

use App\Http\Resources\Admin\Export\CouponExportResource;
use App\Http\Resources\Admin\Export\LoyaltyProgramExportResource;
use App\Http\Resources\Collection;
use App\Models\ClientPass;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CouponModel implements FromCollection, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Date Time',
            'Coupon ID',
            'Client first name',
            'Client last name',
            'Coupon Value',
            'currency',
            'Redeemed amount',
            'Redeemed location',
            'Staff name'
        ];
    }

    public function collection()
    {
        $clients = auth()->user()->clients()->pluck('id');
        return new Collection(CouponExportResource::collection(ClientPass::whereIn('client_id', $clients)->get()));
    }

}
