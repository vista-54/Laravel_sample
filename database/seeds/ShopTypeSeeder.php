<?php

use App\Models\ShopType;
use Illuminate\Database\Seeder;

class ShopTypeSeeder extends Seeder
{
    protected $types = [
        'Supermarket',
        'Supermarket Premium',
        'Mini Mart',
        'Popup Store',
        'Shop',
        'Restaurant'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect($this->types)->each(function ($type) {
            ShopType::create(['name' => $type]);
        });
    }
}
