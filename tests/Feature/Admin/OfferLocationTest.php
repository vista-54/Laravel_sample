<?php

namespace Tests\Feature\Admin;

use App\Models\LoyaltyProgram;
use App\Models\Offer;
use App\Models\OfferLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OfferLocationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $lp;
    protected $offer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);
        $this->lp = factory(LoyaltyProgram::class)->create(['user_id' => $this->user->id]);
        $this->offer = factory(Offer::class)->create(['loyalty_program_id' => $this->lp->id]);
    }

    /**
     * test getting list of locations by offer id.
     *
     * @return void
     */
    public function testGetLocationListByOfferId()
    {
        //Given
        factory(OfferLocation::class, 20)->create(['offer_id' => $this->offer->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/offer/' . $this->offer->id . '/locations');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create offer location
     */
    public function testCreateOfferLocation()
    {
        //Given
        $data = [
            'offer_id' => $this->offer->id,
            'latitude' => '55,5555',
            'longitude' => '99,9999',
            'params' => 'test',
        ];

        //When
        $response = $this->actingAs($this->user)->post('api/admin/offer-location', $data);

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
     * test update Offer location
     */
    public function testUpdateOffer()
    {
        //Given
        $location = factory(OfferLocation::class)->create(['offer_id' => $this->offer->id]);
        $data = [
            'latitude' => '55,5555',
            'longitude' => '99,9999',
            'params' => 'test',
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/offer-location/' . $location->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
                'message' => true,
                'data' => $data
            ]);
    }

    /**
     * test delete Offer
     */
    public function testDeleteOffer()
    {
        //Given
        $location = factory(OfferLocation::class)->create(['offer_id' => $this->offer->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/offer-location/' . $location->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
