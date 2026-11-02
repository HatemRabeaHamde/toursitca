<?php

namespace Tests\Feature\Admin;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Domain\Payout\Models\Payout;
use App\Domain\Review\Models\Review;
use App\Models\User;
use Database\Seeders\AdminQaSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminQaSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixtures_cover_workflows_without_publishing_synthetic_content(): void
    {
        Mail::fake();
        Notification::fake();
        putenv('QA_SEED_PASSWORD=QA-Test-Password-4819');
        try {
            $this->seed(AdminQaSeeder::class);
            $this->assertSame(29, User::count());
            $this->assertSame(8, Agency::count());
            $this->assertSame(16, Host::count());
            $this->assertSame(24, Experience::count());
            $this->assertSame(72, ExperienceOption::count());
            $this->assertSame(384, Availability::count());
            $this->assertSame(96, Booking::count());
            $this->assertSame(48, Payout::count());
            $this->assertSame(24, Review::count());
            $this->assertSame(0, Experience::where('status', 'published')->count());
            $this->assertSame(0, Host::where('is_active', true)->count());
            $this->assertSame(0, Review::where('is_visible', true)->count());
            $admin = User::where('email', 'qa-admin@tourstica.test')->firstOrFail();
            $agent = User::where('email', 'qa-agent01@tourstica.test')->firstOrFail();
            $client = User::where('email', 'qa-client01@tourstica.test')->firstOrFail();
            $this->assertTrue($admin->hasRole('admin'));
            $this->assertTrue($agent->hasRole('travel_agency'));
            $this->assertSame('active', $agent->agency->status);
            $this->assertTrue($client->hasRole('user'));
            $this->assertTrue(Hash::check('QA-Test-Password-4819', $admin->password));
            $private = Booking::where('booking_type', 'private')->firstOrFail();
            $this->assertSame(10, $private->availability->booked_seats);
            $this->assertSame('1000.00', $private->total_price);
            $this->assertSame('150.00', $private->commission_amount);
            $this->assertSame('850.00', $private->payout->amount);
            $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
            $this->actingAs($agent)->get('/agency/dashboard')->assertOk();
            $this->actingAs($client)->get('/en/dashboard/bookings')->assertOk();
            $experience = Experience::firstOrFail();
            $experience->update(['title' => ['en' => 'Manually edited QA record']]);
            $this->seed(AdminQaSeeder::class);
            $this->assertSame(96, Booking::count());
            $this->assertSame('Manually edited QA record', $experience->fresh()->title);
            Mail::assertNothingSent();
            Notification::assertNothingSent();
        } finally {
            putenv('QA_SEED_PASSWORD');
        }
    }

    public function test_account_collision_rolls_back_without_resetting_existing_users(): void
    {
        $existing = User::factory()->create(['email' => 'qa-client01@tourstica.test']);
        $hash = $existing->password;
        putenv('QA_SEED_PASSWORD=QA-Test-Password-4819');
        try {
            try {
                $this->seed(AdminQaSeeder::class);
                $this->fail('The collision must abort the fixture batch.');
            } catch (QueryException) {
                $this->assertSame(1, User::count());
                $this->assertSame($hash, $existing->fresh()->password);
                $this->assertSame(0, Agency::count());
            }
        } finally {
            putenv('QA_SEED_PASSWORD');
        }
    }
}
