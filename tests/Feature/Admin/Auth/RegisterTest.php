<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class RegisterTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRegister()
    {
        //When
        $response = $this->json('POST','/api/admin/register', factory(User::class)->make()->toArray() + ['password' => '111111']);

        //Then
        $response->assertJson([
            'success' => true,
        ]);
    }
}
