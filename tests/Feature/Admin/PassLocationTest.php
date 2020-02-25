<?php

namespace Tests\Feature\Admin;

use App\Models\Pass;
use App\Models\PassLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PassLocationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $pass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);
        $this->pass = factory(Pass::class)->create(['user_id' => $this->user->id]);
    }

    /**
     * test getting list of locations by pass id.
     *
     * @return void
     */
    public function testGetLocationListByOfferId()
    {
        //Given
        factory(PassLocation::class, 20)->create(['pass_id' => $this->pass->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/pass/' . $this->pass->id . '/pass-location');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create pass location
     */
    public function testCreatePassLocation()
    {
        //Given
        $data = [
            'pass_id' => $this->pass->id,
            'latitude' => '55,5555',
            'longitude' => '99,9999',
            'params' => 'test',
        ];

        //When
        $response = $this->actingAs($this->user)->post('api/admin/pass-location', $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,

            ]);
    }

    /**
     * test update Offer location
     */
    public function testUpdateOffer()
    {
        //Given
        $location = factory(PassLocation::class)->create(['pass_id' => $this->pass->id]);
        $data = [
            'latitude' => '55,5555',
            'longitude' => '99,9999',
            'params' => 'test',
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/pass-location/' . $location->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,

            ]);
    }

    /**
     * test delete Offer
     */
    public function testDeleteOffer()
    {
        //Given
        $location = factory(PassLocation::class)->create(['pass_id' => $this->pass->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/pass-location/' . $location->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
