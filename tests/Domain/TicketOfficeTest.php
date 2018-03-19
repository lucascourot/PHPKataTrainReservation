<?php

namespace TrainReservationTest\Domain;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\BookingReferenceProvider;
use TrainReservation\Domain\Coach;
use TrainReservation\Domain\ReservationRequest;
use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\ReservedSeat;
use TrainReservation\Domain\TicketOffice;
use TrainReservation\Domain\TrainDataProvider;
use TrainReservation\Domain\TrainId;
use TrainReservation\Domain\TrainTopology;

class TicketOfficeTest extends TestCase
{
    /**
     * @var TrainId
     */
    private $trainId;

    /**
     * @var BookingReference
     */
    private $bookingReference;

    /**
     * @var BookingReferenceProvider
     */
    private $bookingReferenceProvider;

    /**
     * @var TrainDataProvider
     */
    private $trainDataProvider;

    protected function setUp()
    {
        $this->trainId = new TrainId('express_2000');
        $this->bookingReference = new BookingReference('75bcd15');

        $this->bookingReferenceProvider = $this->prophesize(BookingReferenceProvider::class);
        $this->bookingReferenceProvider->fetchNewBookingReference()->willReturn($this->bookingReference);

        $this->trainDataProvider = $this->prophesize(TrainDataProvider::class);
    }

    public function testShouldReserveSeatsWhenTrainIsEmpty()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
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
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(
            $this->trainId,
            [
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
            ]
        )->shouldBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 3));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEquals($this->bookingReference, $reservationConfirmation->getBookingReference());
        $this->assertEquals(
            [
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
            ],
            $reservationConfirmation->getReservedSeats()
        );
    }

    public function testShouldOnlyReserveOneOrMoreSeats()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
            new Coach([
                new AvailableSeat('A1'),
                new AvailableSeat('A2'),
            ]),
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->expectException(\LogicException::class);

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 0));
    }

    public function testShouldNotReserveSeatsWhenTrainReachedOverallCapacityOf70percent()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
            new Coach([
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
                new ReservedSeat('A4', $this->bookingReference),
                new ReservedSeat('A5', $this->bookingReference),
                new ReservedSeat('A6', $this->bookingReference),
                new ReservedSeat('A7', $this->bookingReference),
                new AvailableSeat('A8'),
                new AvailableSeat('A9'),
                new AvailableSeat('A10'),
            ]),
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(Argument::any(), Argument::any())->shouldNotBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 1));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEmpty($reservationConfirmation->getBookingReference()->getReference());
        $this->assertEmpty($reservationConfirmation->getReservedSeats());
    }

    public function testShouldNotExceedOverallTrainCapacityOf70Percent()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
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
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(Argument::any(), Argument::any())->shouldNotBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 8));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEmpty($reservationConfirmation->getBookingReference()->getReference());
        $this->assertEmpty($reservationConfirmation->getReservedSeats());
    }

    public function testShouldIdeallyNotExceedIndividualCoachCapacityOf70Percent()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
            new Coach([
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
                new ReservedSeat('A4', $this->bookingReference),
                new ReservedSeat('A5', $this->bookingReference),
                new ReservedSeat('A6', $this->bookingReference),
                new ReservedSeat('A7', $this->bookingReference),
                new AvailableSeat('A8'),
                new AvailableSeat('A9'),
                new AvailableSeat('A10'),
            ]),
            new Coach([
                new ReservedSeat('B1', $this->bookingReference),
                new ReservedSeat('B2', $this->bookingReference),
                new ReservedSeat('B3', $this->bookingReference),
                new ReservedSeat('B4', $this->bookingReference),
                new ReservedSeat('B5', $this->bookingReference),
                new AvailableSeat('B6'),
                new AvailableSeat('B7'),
                new AvailableSeat('B8'),
                new AvailableSeat('B9'),
                new AvailableSeat('B10'),
            ]),
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(Argument::any(), Argument::any())->shouldBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 1));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEquals($this->bookingReference, $reservationConfirmation->getBookingReference());
        $this->assertEquals(
            [
                new ReservedSeat('B6', $this->bookingReference),
            ],
            $reservationConfirmation->getReservedSeats()
        );
    }

    public function testShouldBreakTheCoachCapacityRuleIfNoAlternative()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
            new Coach([
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
                new ReservedSeat('A4', $this->bookingReference),
                new ReservedSeat('A5', $this->bookingReference),
                new ReservedSeat('A6', $this->bookingReference),
                new ReservedSeat('A7', $this->bookingReference),
                new AvailableSeat('A8'),
                new AvailableSeat('A9'),
                new AvailableSeat('A10'),
            ]),
            new Coach([
                new ReservedSeat('B1', $this->bookingReference),
                new ReservedSeat('B2', $this->bookingReference),
                new ReservedSeat('B3', $this->bookingReference),
                new ReservedSeat('B4', $this->bookingReference),
                new ReservedSeat('B5', $this->bookingReference),
                new ReservedSeat('B6', $this->bookingReference),
                new ReservedSeat('B7', $this->bookingReference),
                new AvailableSeat('B8'),
                new AvailableSeat('B9'),
                new AvailableSeat('B10'),
            ]),
            new Coach([
                new ReservedSeat('C1', $this->bookingReference),
                new ReservedSeat('C2', $this->bookingReference),
                new ReservedSeat('C3', $this->bookingReference),
                new ReservedSeat('C4', $this->bookingReference),
                new ReservedSeat('C5', $this->bookingReference),
                new AvailableSeat('C6'),
                new AvailableSeat('C7'),
                new AvailableSeat('C8'),
                new AvailableSeat('C9'),
                new AvailableSeat('C10'),
            ]),
            new Coach([
                new ReservedSeat('D1', $this->bookingReference),
                new ReservedSeat('D2', $this->bookingReference),
                new ReservedSeat('D3', $this->bookingReference),
                new ReservedSeat('D4', $this->bookingReference),
                new ReservedSeat('D5', $this->bookingReference),
                new ReservedSeat('D6', $this->bookingReference),
                new AvailableSeat('D7'),
                new AvailableSeat('D8'),
                new AvailableSeat('D9'),
                new AvailableSeat('D10'),
            ]),
            new Coach([
                new ReservedSeat('E1', $this->bookingReference),
                new ReservedSeat('E2', $this->bookingReference),
                new ReservedSeat('E3', $this->bookingReference),
                new ReservedSeat('E4', $this->bookingReference),
                new ReservedSeat('E5', $this->bookingReference),
                new ReservedSeat('E6', $this->bookingReference),
                new AvailableSeat('E7'),
                new AvailableSeat('E8'),
                new AvailableSeat('E9'),
                new AvailableSeat('E10'),
            ]),
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(Argument::any(), Argument::any())->shouldBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 4));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEquals($this->bookingReference, $reservationConfirmation->getBookingReference());
        $this->assertEquals(
            [
                new ReservedSeat('C6', $this->bookingReference),
                new ReservedSeat('C7', $this->bookingReference),
                new ReservedSeat('C8', $this->bookingReference),
                new ReservedSeat('C9', $this->bookingReference),
            ],
            $reservationConfirmation->getReservedSeats()
        );
    }

    public function testShouldReserveSeatsInTheSameCoachForTheSameReservation()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
            new Coach([
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
                new ReservedSeat('A4', $this->bookingReference),
                new ReservedSeat('A5', $this->bookingReference),
                new ReservedSeat('A6', $this->bookingReference),
                new AvailableSeat('A7'),
                new AvailableSeat('A8'),
                new AvailableSeat('A9'),
                new AvailableSeat('A10'),
            ]),
            new Coach([
                new ReservedSeat('B1', $this->bookingReference),
                new ReservedSeat('B2', $this->bookingReference),
                new ReservedSeat('B3', $this->bookingReference),
                new ReservedSeat('B4', $this->bookingReference),
                new ReservedSeat('B5', $this->bookingReference),
                new AvailableSeat('B6'),
                new AvailableSeat('B7'),
                new AvailableSeat('B8'),
                new AvailableSeat('B9'),
                new AvailableSeat('B10'),
            ]),
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(Argument::any(), Argument::any())->shouldBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 2));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEquals($this->bookingReference, $reservationConfirmation->getBookingReference());
        $this->assertEquals(
            [
                new ReservedSeat('B6', $this->bookingReference),
                new ReservedSeat('B7', $this->bookingReference),
            ],
            $reservationConfirmation->getReservedSeats()
        );
    }

    public function testShouldReserveSeatsInDifferentCoachesForTheSameReservation()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn(new TrainTopology([
            new Coach([
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
                new ReservedSeat('A4', $this->bookingReference),
                new ReservedSeat('A5', $this->bookingReference),
                new AvailableSeat('A6'),
                new AvailableSeat('A7'),
                new AvailableSeat('A8'),
                new AvailableSeat('A9'),
                new AvailableSeat('A10'),
            ]),
            new Coach([
                new ReservedSeat('B1', $this->bookingReference),
                new ReservedSeat('B2', $this->bookingReference),
                new ReservedSeat('B3', $this->bookingReference),
                new ReservedSeat('B4', $this->bookingReference),
                new ReservedSeat('B5', $this->bookingReference),
                new AvailableSeat('B6'),
                new AvailableSeat('B7'),
                new AvailableSeat('B8'),
                new AvailableSeat('B9'),
                new AvailableSeat('B10'),
            ]),
            new Coach([
                new ReservedSeat('C1', $this->bookingReference),
                new ReservedSeat('C2', $this->bookingReference),
                new ReservedSeat('C3', $this->bookingReference),
                new ReservedSeat('C4', $this->bookingReference),
                new ReservedSeat('C5', $this->bookingReference),
                new AvailableSeat('C6'),
                new AvailableSeat('C7'),
                new AvailableSeat('C8'),
                new AvailableSeat('C9'),
                new AvailableSeat('C10'),
            ]),
        ]));

        // Expect
        $this->trainDataProvider->markSeatsAsReservedFromList(Argument::any(), Argument::any())->shouldBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 6));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEquals($this->bookingReference, $reservationConfirmation->getBookingReference());
        $this->assertEquals(
            [
                new ReservedSeat('A6', $this->bookingReference),
                new ReservedSeat('A7', $this->bookingReference),
                new ReservedSeat('A8', $this->bookingReference),
                new ReservedSeat('A9', $this->bookingReference),
                new ReservedSeat('A10', $this->bookingReference),
                new ReservedSeat('B6', $this->bookingReference),
            ],
            $reservationConfirmation->getReservedSeats()
        );
    }
}
