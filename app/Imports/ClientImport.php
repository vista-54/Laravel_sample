<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class ClientImport implements ToModel
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Client([
            'user_id' => 1,
            'phone' => $row[5],
            'email' => $row[4],
            'password' => \Hash::make('111111'),
            'first_name' => $row[2],
            'last_name' => $row[3],

        ]);
    }
}
