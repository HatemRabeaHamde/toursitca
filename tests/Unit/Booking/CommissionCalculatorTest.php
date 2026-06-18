<?php

namespace Tests\Unit\Booking;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Services\CommissionCalculator;
use App\Domain\Experience\Models\Experience;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    public function test_group_booking_uses_price_per_person_times_participants(): void
    {
        $agency = new Agency(['commission_rate' => '15.00']);
        $experience = new Experience([
            'price_per_person' => '100.00',
            'private_price' => '350.00',
        ]);
        $experience->setRelation('agency', $agency);

        $result = (new CommissionCalculator)->calculate($experience, 'group', 3);

        $this->assertSame('100.00', $result['unit_price']);
        $this->assertSame('300.00', $result['total_price']);
        $this->assertSame('45.00', $result['commission_amount']);
        $this->assertSame('255.00', $result['agency_amount']);
    }

    public function test_private_booking_uses_flat_private_price(): void
    {
        $agency = new Agency(['commission_rate' => '10.00']);
        $experience = new Experience([
            'price_per_person' => '100.00',
            'private_price' => '450.00',
        ]);
        $experience->setRelation('agency', $agency);

        $result = (new CommissionCalculator)->calculate($experience, 'private', 4);

        $this->assertSame('450.00', $result['unit_price']);
        $this->assertSame('450.00', $result['total_price']);
        $this->assertSame('45.00', $result['commission_amount']);
        $this->assertSame('405.00', $result['agency_amount']);
    }
}
