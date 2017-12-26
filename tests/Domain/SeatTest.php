<?php

namespace TrainReservationTest\Domain;

use PHPUnit\Framework\TestCase;
use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\BookingReference;

class SeatTest extends TestCase
{
    public function testAvailableSeatShouldHaveAReference()
    {
        // When
        $availableSeat = new AvailableSeat('A1');

        // Then
        $this->assertSame('A1', $availableSeat->getReference());
    }

    public function testAvailableSeatShouldBeMarkedAsReserved()
    {
        // Given
        $availableSeat = new AvailableSeat('A1');
        $bookingReference = new BookingReference('abc');

        // When
        $reservedSeat = $availableSeat->reserveWith($bookingReference);

        // Then
        $this->assertSame('A1', $reservedSeat->getReference());
        $this->assertSame($bookingReference, $reservedSeat->getBookingReference());
    }
}
