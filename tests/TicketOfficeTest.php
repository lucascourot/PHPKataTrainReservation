<?php

namespace TrainReservationTest;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use TrainReservation\BookingReference;
use TrainReservation\BookingReferenceProvider;
use TrainReservation\Coach;
use TrainReservation\ReservationRequest;
use TrainReservation\AvailableSeat;
use TrainReservation\ReservedSeat;
use TrainReservation\TicketOffice;
use TrainReservation\TrainDataProvider;
use TrainReservation\TrainId;
use TrainReservation\TrainTopology;

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
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn($this->trainWith1CoachAnd10AvailableSeats());

        // Expect
        $this->trainDataProvider->markSeatsAsReserved(
            $this->trainId,
            $this->threeReservedSeats()
        )->shouldBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 3));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEquals($this->bookingReference, $reservationConfirmation->getBookingReference());
        $this->assertEquals(
            $this->threeReservedSeats(),
            $reservationConfirmation->getReservedSeats()
        );
    }

    public function testShouldNotReserveSeatsWhenTrainIsFull()
    {
        // Given
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn($this->trainWithNoAvailableSeat());

        // Expect
        $this->trainDataProvider->markSeatsAsReserved(Argument::any(), Argument::any())->shouldNotBeCalled();

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
        $this->trainDataProvider->fetchTrainTopology($this->trainId)->willReturn($this->trainWith1CoachAnd10AvailableSeats());

        // Expect
        $this->trainDataProvider->markSeatsAsReserved(Argument::any(), Argument::any())->shouldNotBeCalled();

        // When
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider->reveal(), $this->trainDataProvider->reveal());
        $reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, 8));

        // Then
        $this->assertEquals($this->trainId, $reservationConfirmation->getTrainId());
        $this->assertEmpty($reservationConfirmation->getBookingReference()->getReference());
        $this->assertEmpty($reservationConfirmation->getReservedSeats());
    }

    private function trainWith1CoachAnd10AvailableSeats()
    {
        return new TrainTopology([
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
    }

    private function trainWithNoAvailableSeat()
    {
        return new TrainTopology([
            new Coach($this->threeReservedSeats()),
        ]);
    }

    /**
     * @return array
     */
    private function threeReservedSeats(): array
    {
        return [
            new ReservedSeat('A1', $this->bookingReference),
            new ReservedSeat('A2', $this->bookingReference),
            new ReservedSeat('A3', $this->bookingReference),
        ];
    }
}
