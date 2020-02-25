<?php

namespace Tests\Feature\Admin;

use App\Models\Card;
use App\Models\LoyaltyProgram;
use App\Models\Stamps;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;



class StampsTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $lp;
    protected $card;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['verified' => 1]);
        $this->lp = factory(LoyaltyProgram::class)->create(['user_id' => $this->user->id]);
        $this->card = factory(Card::class)->create(['loyalty_program_id' => $this->lp->id]);
    }

    /**
     * test get stamp by id.
     *
     * @return void
     */
    public function testGetUsersCardByLoyaltyProgram()
    {
        //Given
        $stamps = factory(Stamps::class)->create(['card_id' => $this->card->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/stamps/' . $stamps->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test update stamp.
     *
     * @return void
     */
    public function testUpdateStamp()
    {
        //Given
        $stamps = factory(Stamps::class)->create(['card_id' => $this->card->id]);
        $data = [
            'stamps_number' => 123,
            'background_color' => '#123456',
            'stamp_color' => '#123456',
            'unstamp_color' => '#123456',
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/stamps/' . $stamps->id, $data);

        //Then
        $response->assertStatus(200);
    }
}
