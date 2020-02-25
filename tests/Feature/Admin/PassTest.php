<?php

namespace Tests\Feature\Admin;

use App\Models\Pass;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PassTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;


    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);

    }

    /**
     * test getting list of user passes.
     *
     * @return void
     */
    public function testGetPassListByUser()
    {
        //Given
        factory(Pass::class, 20)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/pass');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test get one pass by id
     */
    public function testGetOnePass()
    {
        //Given
        $pass = factory(Pass::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/pass/' . $pass->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create pass
     */
    public function testCreatePass()
    {
        //Given
        $data = [
            'user_id' => $this->user->id,
            'title' => 'test title',
            'description' => 'test desc',
        ];

        //When
        $response = $this->actingAs($this->user)->post('api/admin/pass', $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test update pass
     */
    public function testUpdatePass()
    {
        //Given
        $pass = factory(Pass::class)->create(['user_id' => $this->user->id]);
        $data = [
            'title' => 'test title',
            'description' => 'test desc',
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/pass/' . $pass->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }

    /**
     * test delete pass
     */
    public function testDeletePass()
    {
        //Given
        $pass = factory(Pass::class)->create(['user_id' => $this->user->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/pass/' . $pass->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
