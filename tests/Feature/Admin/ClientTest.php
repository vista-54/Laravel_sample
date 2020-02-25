<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);
    }


    /**
     * test getting list of clients by user id
     *
     * @return void
     */
    public function testGetUsersClients()
    {
        //Given
        factory(Client::class, 20)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/merchant-client/' . $this->user->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test get one client by id.
     *
     * @return void
     */
    public function testGetOneScoreByLoyaltyProgram()
    {
        //Given
        $client = factory(Client::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/client/' . $client->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create client
     */
    public function testCreateScore()
    {
        //Given
        $data = [
            'user_id' => $this->user->id,
            'phone' => '646436436',
            'email' => 'qwerty@asdf.com',
            'password' => '111111',
            'address' => null,
        ];
        //When
        $response = $this->actingAs($this->user)->post('api/admin/client', $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test update client
     */
    public function testUpdateScore()
    {
        //Given
        $client = factory(Client::class)->create(['user_id' => $this->user->id]);
        $data = [
            'phone' => 43214231234,
            'email' => 'qwerty@asdf.com',
            'password' => '111111',
            'address' => null,
            'first_name' => 'test',
            'last_name' => 'surname',
        ];
        //When
        $response = $this->actingAs($this->user)->put('api/admin/client/' . $client->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test delete client
     */
    public function testDeleteScore()
    {
        //Given
        $score = factory(Client::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/client/' . $score->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
