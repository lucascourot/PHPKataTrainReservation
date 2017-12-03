<?php

namespace TrainReservationTest\Domain;

use PHPUnit\Framework\TestCase;
use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\OptionOfReservation;
use TrainReservation\Domain\ReservedSeat;

class OptionOfReservationTest extends TestCase
{
    public function testShouldNotBeSatisfiedByDefault()
    {
        // When
        $optionOfReservation = new OptionOfReservation(3);

        // Then
        $this->assertFalse($optionOfReservation->isSatisfied());
    }

    public function testShouldMarkSeatsAsReserved()
    {
        // Given
        $optionOfReservation = new OptionOfReservation(3);

        // When
        $optionOfReservation->markSeatsAsReserved([
            new AvailableSeat('A1'),
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
        ]);

        // Then
        $this->assertTrue($optionOfReservation->isSatisfied());
    }

    public function testShouldNotMarkAReservedSeatAsReservedAgain()
    {
        // Given
        $optionOfReservation = new OptionOfReservation(1);

        // When
        $optionOfReservation->markSeatsAsReserved([
            new ReservedSeat('A1', new BookingReference('abc')),
        ]);

        // Then
        $this->assertFalse($optionOfReservation->isSatisfied());
    }

    public function testShouldBookReservedSeats()
    {
        // Given
        $optionOfReservation = new OptionOfReservation(3);
        $bookingReference = new BookingReference('abc');

        // When
        $optionOfReservation->markSeatsAsReserved([
            new AvailableSeat('A1'),
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
        ]);

        $reservedSeats = $optionOfReservation->reserveSeatsWith($bookingReference);

        // Then
        $this->assertTrue($optionOfReservation->isSatisfied());
        $this->assertEquals([
            new ReservedSeat('A1', $bookingReference),
            new ReservedSeat('A2', $bookingReference),
            new ReservedSeat('A3', $bookingReference),
        ], $reservedSeats);
    }

    public function testShouldNotBookReservedSeatsWhenOptionIsNotSatisfied()
    {
        // Given
        $optionOfReservation = new OptionOfReservation(3);
        $bookingReference = new BookingReference('abc');

        // Excpect
        $this->expectException(\LogicException::class);

        // When
        $optionOfReservation->markSeatsAsReserved([
            new AvailableSeat('A1'),
            new AvailableSeat('A2'),
        ]);

        $optionOfReservation->reserveSeatsWith($bookingReference);

        // Then
        $this->assertFalse($optionOfReservation->isSatisfied());
    }

    public function testShouldNotMarkAnyOtherSeatAsReservedIfAlreadySatisfied()
    {
        // Given
        $optionOfReservation = new OptionOfReservation(1);
        $bookingReference = new BookingReference('abc');

        // When
        $optionOfReservation->markSeatsAsReserved([
            new AvailableSeat('A1'),
        ]);
        $optionOfReservation->markSeatsAsReserved([
            new AvailableSeat('A2'),
        ]);

        $reservedSeats = $optionOfReservation->reserveSeatsWith($bookingReference);

        // Then
        $this->assertTrue($optionOfReservation->isSatisfied());
        $this->assertEquals([
            new ReservedSeat('A1', $bookingReference),
        ], $reservedSeats);
    }
}
