<?php

namespace Tests\Feature\Admin;

use App\Models\Card;
use App\Models\LoyaltyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class CardTest extends TestCase
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
     * test get self user card.
     *
     * @return void
     */
    public function testGetUsersCardByLoyaltyProgram()
    {
        //Given
        factory(Card::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/card');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test update card
     */
    public function testUpdateCard()
    {
        //Given
        $card = factory(Card::class)->create(['loyalty_program_id' => $this->lp->id]);
        $data = [
            'background_color' => 'test_data',
            'foreground_color' => 'test_data',
            'label_color' => 'test_data',
            'points_head' => 'test_data',
            'points_value' => 'test_data',
            'customer_head' => 'test_data',
            'customer_value' => 'test_data',
            'flip_head' => 'test_data',
            'flip_value' => 'test_data',
            'loyalty_profile' => 1,
            'loyalty_offers' => 1,
            'loyalty_contact' => 1,
            'loyalty_terms' => 1,
            'loyalty_terms_value' => 'super test text',
            'loyalty_message' => '1',
            'icon' => null,
            'customer_id' => '${customer_id}',
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/card/' . $card->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
                'message' => true,
                'data' => $data
            ]);
    }
}
