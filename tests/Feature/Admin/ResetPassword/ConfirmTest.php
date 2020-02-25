<?php

namespace Tests\Feature\Admin\ResetPassword;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConfirmTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testConfirmEmail()
    {
        //Given
        $user = factory(User::class)->create(['verified' => 1]);
        $this->json('POST', '/api/admin/forgot',
            [
                'email' => $user->email
            ]);
        $code = PasswordReset::first();

        //When
        $response = $this->json('POST', '/api/admin/confirm',
            [
                'code' => $code->code
            ]);

        //Then
        $response->assertJson([
            'success' => true
        ]);
    }
}
