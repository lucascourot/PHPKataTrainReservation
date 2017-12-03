<?php

namespace TrainReservationTest;

use PHPUnit\Framework\TestCase;
use TrainReservation\AvailableSeat;
use TrainReservation\BookingReference;
use TrainReservation\Coach;
use TrainReservation\ReservedSeat;

class CoachTest extends TestCase
{
    public function testShouldNotExceedIdealCapacityUnder70Percent()
    {
        // When
        $coach = new Coach([
            new AvailableSeat('A1'),
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
            new AvailableSeat('A4'),
            new AvailableSeat('A5'),
            new AvailableSeat('A6'),
            new AvailableSeat('A7'),
            new AvailableSeat('A8'),
            new AvailableSeat('A9'),
            new AvailableSeat('A10'),
        ]);

        // Then
        $this->assertFalse($coach->exceedsIdealCapacityWith(7));
    }

    public function testShouldExceedIdealCapacityAbove70Percent()
    {
        // When
        $coach = new Coach([
            new AvailableSeat('A1'),
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
            new AvailableSeat('A4'),
            new AvailableSeat('A5'),
            new AvailableSeat('A6'),
            new AvailableSeat('A7'),
            new AvailableSeat('A8'),
            new AvailableSeat('A9'),
            new AvailableSeat('A10'),
        ]);

        // Then
        $this->assertTrue($coach->exceedsIdealCapacityWith(8));
    }

    public function testShouldReturnNumberOfReservedSeats()
    {
        // When
        $coach = new Coach([
            new ReservedSeat('A1', new BookingReference('abc')),
            new AvailableSeat('A2'),
        ]);

        // Then
        $this->assertSame(1, $coach->getNumberOfAlreadyReservedSeats());
    }

    public function testShouldReturnAskedAvailableSeatsWhenHasEnough()
    {
        // When
        $coach = new Coach([
            new ReservedSeat('A1', new BookingReference('abc')),
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
        ]);

        // Then
        $this->assertEquals([
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
        ], $coach->getAvailableSeatsFor(2));
    }

    public function testShouldReturnAskedAvailableSeatsEvenWhenNotEnough()
    {
        // When
        $coach = new Coach([
            new ReservedSeat('A1', new BookingReference('abc')),
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
        ]);

        // Then
        $this->assertEquals([
            new AvailableSeat('A2'),
            new AvailableSeat('A3'),
        ], $coach->getAvailableSeatsFor(5));
    }
}
