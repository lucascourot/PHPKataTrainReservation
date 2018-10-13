<?php

declare(strict_types=1);

namespace TrainReservation;

use TrainReservation\Domain\ProvidesBookingReference;
use TrainReservation\Domain\ProvidesTrainData;
use TrainReservation\Domain\ReservationConfirmation;
use TrainReservation\Domain\ReservationRequest;
use TrainReservation\Domain\ReserveSeats;
use TrainReservation\Domain\TicketOffice;
use TrainReservation\Domain\TrainId;

final class Hexagon implements ReserveSeats
{
    /**
     * @var ProvidesBookingReference
     */
    private $providesBookingReference;

    /**
     * @var ProvidesTrainData
     */
    private $providesTrainData;

    public function __construct(ProvidesBookingReference $providesBookingReference, ProvidesTrainData $providesTrainData)
    {
        $this->providesBookingReference = $providesBookingReference;
        $this->providesTrainData = $providesTrainData;
    }

    public function reserveSeats(string $trainId, int $nbSeats): ReservationConfirmation
    {
        $ticketOffice = new TicketOffice($this->providesBookingReference, $this->providesTrainData);

        return $ticketOffice->makeReservation(new ReservationRequest(new TrainId($trainId), $nbSeats));
    }
}
