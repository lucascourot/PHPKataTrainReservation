<?php

namespace TrainReservation\Infrastructure\Adapters\Http;

use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\BookingReferenceProvider;

final class BookingReferenceProviderAdapter implements BookingReferenceProvider
{
    public function fetchNewBookingReference(): BookingReference
    {
        return new BookingReference('abc');
    }
}
