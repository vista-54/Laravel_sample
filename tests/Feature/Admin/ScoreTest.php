<?php

namespace Tests\Feature\Admin;

use App\Models\LoyaltyProgram;
use App\Models\Score;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class ScoreTest extends TestCase
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
     * test getting list of scores by users loyalty program.
     *
     * @return void
     */
    public function testGetUsersSelfScoreList()
    {
        //Given
        factory(Score::class, 3)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/score');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test get one score by id.
     *
     * @return void
     */
    public function testGetOneScoreByLoyaltyProgram()
    {
        //Given
        $score = factory(Score::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/score/' . $score->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test create score
     */
    public function testCreateScore()
    {
        //Given
        $data = [
            'loyalty_program_id' => $this->lp->id,
            'set_email' => 0,
            'set_phone' => 0,
            'set_card' => 0,
            'scan_card' => 0,
        ];
        //When
        $response = $this->actingAs($this->user)->post('api/admin/score', $data);

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
     * test update score
     */
    public function testUpdateScore()
    {
        //Given
        $score = factory(Score::class)->create(['loyalty_program_id' => $this->lp->id]);
        $data = [
            'loyalty_program_id' => $this->lp->id,
            'set_email' => 1,
            'set_phone' => 1,
            'set_card' => 1,
            'scan_card' => 1,
        ];
        //When
        $response = $this->actingAs($this->user)->put('api/admin/score/' . $score->id, $data);

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
     * test delete score
     */
    public function testDeleteScore()
    {
        //Given
        $score = factory(Score::class)->create(['loyalty_program_id' => $this->lp->id]);

        //When
        $response = $this->actingAs($this->user)->delete('api/admin/score/' . $score->id);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }
}
