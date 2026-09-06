<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_registration_screen_can_be_rendered(): void
    {
        $this->get(route('agency.register'))->assertOk();
    }

    public function test_agency_can_register_and_is_redirected_to_pending_review(): void
    {
        $response = $this->post(route('agency.register.store'), [
            'owner_name' => 'Agency Owner',
            'email' => 'agency@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'agency_name' => 'Atlas Trips',
            'city' => 'Marrakesh',
            'phone' => '+212600000000',
            'description' => 'Local agency.',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('agency.pending'));
        $this->assertDatabaseHas(Agency::class, [
            'name' => 'Atlas Trips',
            'status' => 'pending',
            'city' => 'Marrakesh',
        ]);
    }
}
