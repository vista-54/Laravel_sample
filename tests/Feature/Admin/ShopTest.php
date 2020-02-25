<?php

namespace Tests\Feature\Admin;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);
    }


    /**
     * test getting list of users shops
     *
     * @return void
     */
    public function testGetUsersShops()
    {
        //Given
        factory(Shop::class, 20)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/shop');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test get one shop by id.
     *
     * @return void
     */
    public function testGetOneShop()
    {
        //Given
        $shop = factory(Shop::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/shop/' . $shop->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create shop
     */
    public function testCreateScore()
    {
        //Given
        $data = [
            'user_id' => $this->user->id,
            'number' => 123,
            'name' => 'test name'
        ];
        //When
        $response = $this->actingAs($this->user)->post('api/admin/shop', $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test update shop
     */
    public function testUpdateScore()
    {
        //Given
        $shop = factory(Shop::class)->create(['user_id' => $this->user->id]);
        $data = [
            'user_id' => $this->user->id,
            'number' => 100,
            'name' => 'test name'
        ];
        //When
        $response = $this->actingAs($this->user)->put('api/admin/shop/' . $shop->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test delete shop
     */
    public function testDeleteScore()
    {
        //Given
        $shop = factory(Shop::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/shop/' . $shop->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
