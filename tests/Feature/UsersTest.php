<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->json('GET','/api/admin/user', [], ['Authorization' => 'Bearer ' . getenv('TEST_TOKEN')]);
        $response->assertOk();
    }
}
