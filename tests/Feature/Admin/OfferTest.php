<?php

namespace Tests\Feature\Admin;

use App\Models\LoyaltyProgram;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OfferTest extends TestCase
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
     * test getting list of Offers by users loyalty program.
     *
     * @return void
     */
    public function testGetUsersSelfOfferList()
    {
        //Given
        factory(Offer::class, 20)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/offer');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test get one offer by id.
     *
     * @return void
     */
    public function testGetOneOfferByLoyaltyProgram()
    {
        //Given
        $offer = factory(Offer::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/offer/' . $offer->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create offer
     */
    public function testCreateOffer()
    {
        //Given
        $data = [
            'loyalty_program_id' => $this->lp->id,
            'name' => 'test',
            'description' => 'test',
            'start_date' => null,
            'end_date' => null,
            'points_cost' => 12,
            'customer_limit' => null,
            'availability_count' => null,
            'notify' => null
        ];
        //When
        $response = $this->actingAs($this->user)->post('api/admin/offer', $data);

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
     * test update Offer
     */
    public function testUpdateOffer()
    {
        //Given
        $offer = factory(Offer::class)->create(['loyalty_program_id' => $this->lp->id]);
        $data = [
            'name' => 'test1',
            'description' => 'test1',
            'start_date' => null,
            'end_date' => null,
            'points_cost' => 12,
            'customer_limit' => 11,
            'availability_count' => 44,
            'notify' => 'test notify'
        ];
        //When
        $response = $this->actingAs($this->user)->put('api/admin/offer/' . $offer->id, $data);

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
     * test update Offer-Card
     */
    public function testUpdateOfferCard()
    {
        //Given
        $offer = factory(Offer::class)->create(['loyalty_program_id' => $this->lp->id]);
        $data = [
            'name' => 'test1',
            'description' => 'test1',
            'start_date' => null,
            'end_date' => null,
            'points_cost' => 12,
            'customer_limit' => 11,
            'availability_count' => 44,
            'notify' => 'test notify'
        ];
        //When
        $response = $this->actingAs($this->user)->put('api/admin/offer/' . $offer->id, $data);

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
        $offer = factory(Offer::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/offer/' . $offer->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
