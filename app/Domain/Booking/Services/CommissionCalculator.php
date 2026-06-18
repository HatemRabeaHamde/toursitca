<?php

namespace App\Domain\Booking\Services;

use App\Domain\Experience\Models\Experience;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

final class CommissionCalculator
{
    /**
     * @return array{unit_price: string, total_price: string, commission_rate: string, commission_amount: string, agency_amount: string}
     */
    public function calculate(Experience $experience, string $bookingType, int $participantsCount): array
    {
        if ($participantsCount < 1) {
            throw new InvalidArgumentException('Participants count must be at least 1.');
        }

        $commissionRate = BigDecimal::of((string) $experience->agency->commission_rate);

        if ($bookingType === 'group') {
            $unitPrice = BigDecimal::of((string) $experience->price_per_person);
            $totalPrice = $unitPrice->multipliedBy($participantsCount);
        } elseif ($bookingType === 'private') {
            if ($experience->private_price === null) {
                throw new InvalidArgumentException('Private booking is not available for this experience.');
            }

            $unitPrice = BigDecimal::of((string) $experience->private_price);
            $totalPrice = $unitPrice;
        } else {
            throw new InvalidArgumentException('Unsupported booking type.');
        }

        $commissionAmount = $totalPrice
            ->multipliedBy($commissionRate)
            ->dividedBy(100, 2, RoundingMode::HALF_UP);
        $agencyAmount = $totalPrice->minus($commissionAmount);

        return [
            'unit_price' => (string) $unitPrice->toScale(2, RoundingMode::HALF_UP),
            'total_price' => (string) $totalPrice->toScale(2, RoundingMode::HALF_UP),
            'commission_rate' => (string) $commissionRate->toScale(2, RoundingMode::HALF_UP),
            'commission_amount' => (string) $commissionAmount->toScale(2, RoundingMode::HALF_UP),
            'agency_amount' => (string) $agencyAmount->toScale(2, RoundingMode::HALF_UP),
        ];
    }
}
