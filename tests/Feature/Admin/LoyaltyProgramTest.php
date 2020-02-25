<?php

namespace Tests\Feature\tests\Feature\Admin;

use App\Models\Card;
use App\Models\ContactsTerm;
use App\Models\LoyaltyProgram;
use App\Models\Score;
use App\Models\Stamps;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoyaltyProgramTest extends TestCase
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
     * get users self loyalty program data
     *
     * @return void
     */
    public function testGetLoyaltyProgram()
    {
        //Given
        $card = factory(Card::class)->create(['loyalty_program_id' => $this->lp->id]);
        factory(Stamps::class)->create(['card_id' => $card->id]);

        //When
        $response = $this->actingAs($this->user)
            ->get('api/admin/program/user');

        //Then
        $response->assertStatus(200)
            ->assertJsonStructure([
                'entity' => [
                    'id',
                    'user_id',
                    'title',
                    'description',
                    'country',
                    'language',
                    'link',
                    'currency',
                    'currency_value',
                    'created_at',
                    'updated_at',
                    'card' => [
                        'id',
                        'loyalty_program_id',
                        'background_color',
                        'foreground_color',
                        'label_color',
                        'points_head',
                        'points_value',
                        'customer_head',
                        'customer_value',
                        'flip_head',
                        'flip_value',
                        'loyalty_profile',
                        'loyalty_offers',
                        'loyalty_contact',
                        'loyalty_terms',
                        'loyalty_terms_value',
                        'loyalty_message',
                        'icon',
                        'background_image',
                        'customer_id',
                        'created_at',
                        'updated_at',
                        'stamps' => [
                            'card_id',
                            'stamps_number',
                            'background_color',
                            'background_image',
                            'stamp_color',
                            'unstamp_color',
                            'stamp_image',
                            'unstamp_image',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * get loyalty program data by user_id
     *
     * @return void
     */
    public function testGetLoyaltyProgramByUserId()
    {
        //Given
        factory(Stamps::class)->create(['card_id' => $this->card->id]);

        //When
        $response = $this->actingAs($this->user)
            ->get('api/admin/program/user/' . $this->user->id);

        //Then
        $response->assertStatus(200)
            ->assertJsonStructure([
                'entity' => [
                    'id',
                    'user_id',
                    'title',
                    'description',
                    'country',
                    'language',
                    'link',
                    'currency',
                    'currency_value',
                    'created_at',
                    'updated_at',
                    'card' => [
                        'id',
                        'loyalty_program_id',
                        'background_color',
                        'foreground_color',
                        'label_color',
                        'points_head',
                        'points_value',
                        'customer_head',
                        'customer_value',
                        'flip_head',
                        'flip_value',
                        'loyalty_profile',
                        'loyalty_offers',
                        'loyalty_contact',
                        'loyalty_terms',
                        'loyalty_terms_value',
                        'loyalty_message',
                        'icon',
                        'background_image',
                        'customer_id',
                        'created_at',
                        'updated_at',
                        'stamps' => [
                            'card_id',
                            'stamps_number',
                            'background_color',
                            'background_image',
                            'stamp_color',
                            'unstamp_color',
                            'stamp_image',
                            'unstamp_image',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * get loyalty program settings
     *
     * @return void
     */
    public function testGetLoyaltyProgramSettings()
    {
        //Given
        $contactTerm = factory(ContactsTerm::class)->create(['loyalty_program_id' => $this->lp->id]);
        $score = factory(Score::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)
            ->get('api/admin/program/' . $this->lp->id . '/settings/');

        //Then
        $response->assertStatus(200)
            ->assertJsonStructure([
                'entity' => [
                    'program' => $this->lp->getFillable(),
                    'terms' => [
                        $contactTerm->getFillable()
                    ],
                    'score' => [
                        $score->getFillable()
                    ]
                ]
            ]);
    }

    /**
     * update loyalty program data
     *
     * @return void
     */
    public function testUpdateLoyaltyProgram()
    {
        //Given
        factory(ContactsTerm::class)->create(['loyalty_program_id' => $this->lp->id]);
        factory(Score::class)->create(['loyalty_program_id' => $this->lp->id]);

        $data = [
            'user_id' => $this->user->id,
            'title' => 'test title',
            'description' => 'test desc',
            'country' => 'Russia',
            'language' => 'ru',
            'icon' => 'test icon',
            'link' => 'test link',
            'set_email' => 1,
            'set_phone' => 1,
            'set_card' => 1,
            'scan_card' => 1,
            'company_name' => 'test comp name',
            'address' => 'test address',
            'website' => 'http://etst.site',
            'email' => 'test@email.super',
            'phone' => 'test phone',
            'conditions' => 'test conditions',
        ];

        //When
        $response = $this->actingAs($this->user)
            ->put('api/admin/program/' . $this->lp->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
                'message' => true,
                'data' => null
            ]);
    }
}
