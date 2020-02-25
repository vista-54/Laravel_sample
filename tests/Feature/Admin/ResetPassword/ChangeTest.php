<?php

namespace Tests\Feature\Admin\ResetPassword;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ChangeTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testChangePassword()
    {
        //Given
        $user = factory(User::class)->create(['verified' => 1]);
        $this->json('POST', '/api/admin/forgot',
            [
                'email' => $user->email
            ]);
        $code = PasswordReset::first();

        //When
        $response = $this->json('POST', '/api/admin/change',
            [
                'code' => $code->code,
                'password' => 222222
            ]);

        //Then
        $response->assertJson([
            'success' => true
        ]);

        $this->json('POST', '/api/admin/login',
            [
                'email' => $user->email,
                'password' => '222222'
            ]);
        $this->assertAuthenticated('api');
    }
}
