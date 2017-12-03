<?php

namespace TrainReservationTest;

use PHPUnit\Framework\TestCase;
use TrainReservation\AvailableSeat;
use TrainReservation\BookingReference;
use TrainReservation\Coach;
use TrainReservation\TrainTopology;

class TrainTopologyTest extends TestCase
{
    public function testShouldReserveSeatsWhenEmpty()
    {
        // Given
        $trainTopology = new TrainTopology([
            new Coach([
                new AvailableSeat('A1'),
                new AvailableSeat('A2'),
                new AvailableSeat('A3'),
                new AvailableSeat('A4'),
                new AvailableSeat('A5'),
            ]),
        ]);

        // When
        $optionOfReservation = $trainTopology->tryToReserveSeats(1);

        // Then
        $this->assertTrue($optionOfReservation->isSatisfied());
    }

    public function testShouldNotReserveSeatsWhenExceedsOverallTrainCapacityOf70Percent()
    {
        // Given
        $trainTopology = new TrainTopology([
            new Coach([
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
            ]),
        ]);

        // When
        $optionOfReservation = $trainTopology->tryToReserveSeats(8);

        // Then
        $this->assertFalse($optionOfReservation->isSatisfied());
    }

    public function testShouldNotReserveSeatsIfNotSuccessfulReservation()
    {
        // Given
        $trainTopology = new TrainTopology([
            new Coach([
                new AvailableSeat('A1'),
                new AvailableSeat('A2'),
            ]),
        ]);
        $bookingReference = new BookingReference('75bcd15');

        // Expect
        $this->expectException(\LogicException::class);

        // When
        $optionOfReservation = $trainTopology->tryToReserveSeats(5);
        $optionOfReservation->reserveSeatsWith($bookingReference);

        // Then
        $this->assertFalse($optionOfReservation->isSatisfied());
    }
}
