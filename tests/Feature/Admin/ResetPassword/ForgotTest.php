<?php

namespace Tests\Feature\Admin\ResetPassword;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ForgotTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testForgotPass()
    {
        //Given
        $user = factory(User::class)->create(['verified' => 1]);

        //When
        $response = $this->json('POST', '/api/admin/forgot',
            [
                'email' => $user->email
            ]);

        //Then
        $response->assertJson([
            'success' => true
        ]);
    }
}
