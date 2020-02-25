<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'department' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('111111'),
            'role' => User::ROLE_SUPER_ADMIN,
            'first_name' => 'test name',
            'last_name' => 'test surname',
            'timezone' => '2',
            'verified' => 1
        ]);

        User::create([
            'department' => 'department',
            'email' => 'efauvel@gmail.com',
            'password' => Hash::make('111111'),
            'role' => User::ROLE_MERCHANT,
            'first_name' => 'Efauvel',
            'last_name' => 'Test',
            'timezone' => '2',
            'verified' => 1
        ]);

        DB::table('clients')->insert([
            'user_id' => 1,
            'phone' => '37894561237',
            'email' => 'test@email1.com',
            'password' => Hash::make('111111'),
            'first_name' => 'test name1',
            'last_name' => 'test surname1',
            'timezone' => '2'
        ]);

        DB::table('currencies')->insert(['name' => 'MYR']);
        DB::table('currencies')->insert(['name' => 'PHP']);
        DB::table('currencies')->insert(['name' => 'SGD']);
        DB::table('currencies')->insert(['name' => 'THB']);
        DB::table('currencies')->insert(['name' => 'USD']);
        DB::table('currencies')->insert(['name' => 'VND']);

        DB::table('races')->insert(['name' => 'Thai']);
        DB::table('races')->insert(['name' => 'Malay']);
        DB::table('races')->insert(['name' => 'Chinese']);
        DB::table('races')->insert(['name' => 'Indians']);
        DB::table('races')->insert(['name' => 'Other']);
    }
}
