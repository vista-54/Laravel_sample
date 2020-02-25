<?php

namespace App\Exports\Model;

use App\Http\Resources\Admin\Export\LoyaltyProgramExportResource;
use App\Http\Resources\Admin\Export\TransactionExportResource;
use App\Http\Resources\Collection;
use App\Models\ClientPass;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionModel implements FromCollection, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Date Time',
            'Loyalty card ID',
            'Client first name',
            'Client last name',
            'Transaction Value',
            'currency',
            'point added',
            'post debited',
            'value per point',
            'Location',
            'Staff name'
        ];
    }

    public function collection()
    {
        $clients = auth()->user()->clients()->pluck('id');
        return new Collection(TransactionExportResource::collection(Transaction::whereIn('client_id', $clients)->get()));
    }

}
