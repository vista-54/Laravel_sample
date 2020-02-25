<?php

namespace Tests\Feature;

use App\Models\ContactsTerm;
use App\Models\LoyaltyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TermsTest extends TestCase
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
     * test terms list for users self loyalty program
     *
     * @return void
     */
    public function testGetTermsByLoyaltyProgram()
    {
        //Given
        factory(ContactsTerm::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/terms');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test update terms
     *
     * @return void
     */
    public function testUpdateTerms()
    {
        //Given
        $terms = factory(ContactsTerm::class)->create(['loyalty_program_id' => $this->lp->id]);
        $data = [
            'company_name' => 'test company_name',
            'address' => 'test address',
            'website' => 'http://test.website',
            'email' => 'super@email.test',
            'phone' => 'test phone',
            'conditions' => 'my test conditions'
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/terms/' . $terms->id, $data);

        //Then
        $response->assertStatus(200)->assertJson([
            'status' => 'success',
            'status_code' => 201,
            'message' => true,
            'data' => $data
        ]);;
    }
}
