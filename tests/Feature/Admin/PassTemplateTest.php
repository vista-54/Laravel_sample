<?php

namespace Tests\Feature\Admin;

use App\Models\Pass;
use App\Models\PassTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PassTemplateTest extends TestCase
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
     * test getting pass template by pass id.
     *
     * @return void
     */
    public function testGetPassTemplateByPassId()
    {
        //Given
        factory(PassTemplate::class)->create(['pass_id' => $this->pass->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/pass/' . $this->pass->id . '/pass-template');

        //Then
        $response->assertStatus(200);
    }

    /**
     * test get one pass template
     */
    public function testGetOnePassTemplate()
    {
        //Given
        $template = factory(PassTemplate::class)->create(['pass_id' => $this->pass->id]);

        //When
        $response = $this->actingAs($this->user)->get('api/admin/pass-template/' . $template->id);

        //Then
        $response->assertStatus(200);
    }

    /**
     * test update pass template
     */
    public function testUpdatePassTemplate()
    {
        //Given
        $template = factory(PassTemplate::class)->create(['pass_id' => $this->pass->id]);
        $data = [
            'background_color' => 'test data',
            'foreground_color' => 'test data',
            'label_color' => 'test data',
            'points_head' => 'test data',
            'points_value' => 'test data',
            'offer_head' => 'test data',
            'offer_value' => 'test data',
            'customer_head' => 'test data',
            'customer_value' => 'test data',
            'flip_head' => 'test data',
            'flip_value' => 'test data',
            'back_side_head' => 'test data',
            'back_side_value' => 'test data',
            'icon' => 'test data',
            'background_image' => 'test data',
            'stripe_image' => 'test data',
            'customer_id' => 'test data',
        ];

        //When
        $response = $this->actingAs($this->user)->put('api/admin/pass-template/' . $template->id, $data);

        //Then
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'status_code' => 201,
            ]);
    }
}
