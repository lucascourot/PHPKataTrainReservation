<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

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
