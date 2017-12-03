<?php

namespace TrainReservation\Infrastructure\Adapters\Http;

use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\Coach;
use TrainReservation\Domain\ReservedSeat;
use TrainReservation\Domain\TrainDataProvider;
use TrainReservation\Domain\TrainId;
use TrainReservation\Domain\TrainTopology;

final class TrainDataProviderAdapter implements TrainDataProvider
{
    private $bookingReference;

    public function __construct()
    {
        $this->bookingReference = new BookingReference('abc');
    }

    public function fetchTrainTopology(TrainId $trainId): TrainTopology
    {
        return new TrainTopology([
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
        ]);
    }

    public function markSeatsAsReserved(TrainId $trainId, array $reservedSeats)
    {
//        echo 'Reserved!';
    }
}
