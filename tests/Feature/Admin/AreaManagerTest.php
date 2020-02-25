<?php

namespace Tests\Feature\Admin;

use App\Models\AreaManager;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class AreaManagerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);
    }


    /**
     * test getting list of users area-managers
     *
     * @return void
     */
    public function testGetUsersAreaManagers()
    {
        //Given
        factory(AreaManager::class, 20)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/area-manager');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test get one area-manager by id.
     *
     * @return void
     */
    public function testGetOneAreaManager()
    {
        //Given
        $manager = factory(AreaManager::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/area-manager/' . $manager->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create area-manager
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
        $response = $this->actingAs($this->user)->post('api/admin/area-manager', $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test update area-manager
     */
    public function testUpdateScore()
    {
        //Given
        $manager = factory(AreaManager::class)->create(['user_id' => $this->user->id]);
        $data = [
            'user_id' => $this->user->id,
            'number' => 100,
            'name' => 'test name'
        ];
        //When
        $response = $this->actingAs($this->user)->put('api/admin/area-manager/' . $manager->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test delete area-manager
     */
    public function testDeleteScore()
    {
        //Given
        $manager = factory(AreaManager::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/area-manager/' . $manager->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
