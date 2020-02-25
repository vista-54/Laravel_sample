<?php

namespace Tests\Feature\Admin;

use App\Models\Location;
use App\Models\LoyaltyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $lp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);
        $this->lp = factory(LoyaltyProgram::class)->create(['user_id' => $this->user->id]);
    }

    /**
     * test get self users list of locations by loyalty program
     *
     * @return void
     */
    public function testGetSelfLocationList()
    {
        //Given
        factory(Location::class, 3)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/location');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create location
     */
    public function testCreateLocation()
    {
        //Given
        $data = [
            'loyalty_program_id' => $this->lp->id,
            'latitude' => '41.56',
            'longitude' => '53.21',
            'params' => 'some data'
        ];

        //When
        $response = $this->actingAs($this->user)->post('api/admin/location', $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
                'message' => $data,
                'data' => null
            ]);
    }

    /**
     * test update location
     */
    public function testUpdateLocation()
    {
        //Given
        $location = factory(Location::class)->create(['loyalty_program_id' => $this->lp->id]);
        $data = [
            'loyalty_program_id' => $this->lp->id,
            'latitude' => '100.12',
            'longitude' => '11.11',
            'params' => 'some data'
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/location/' . $location->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
                'message' => $data,
                'data' => null
            ]);
    }

    public function testDeleteLocation()
    {
        //Given
        $location = factory(Location::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/location/' . $location->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);
    }
}
