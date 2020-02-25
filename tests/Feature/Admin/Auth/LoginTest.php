<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLogin()
    {
        //Given
        $user = factory(User::class)->create(['verified' => 1]);
        //When
        $this->json('POST', '/api/admin/login',
            [
                'email' => $user->email,
                'password' => '111111'
            ]);
        //Then
        $this->assertAuthenticated('api');
    }

    public function testLoginFail()
    {
        //Given
        $user = factory(User::class)->create(['verified' => 1]);

        //When
        $response = $this->json('POST', '/api/admin/login',
            [
                'email' => $user->email,
                'password' => '111112'
            ]);

        //Then
        $response->assertStatus(401)->assertJson(['message' => 'We cant find an account with this credentials.']);
    }
}
