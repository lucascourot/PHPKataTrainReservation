<?php

namespace TrainReservation\Domain;

class TicketOffice
{
    /**
     * @var BookingReferenceProvider
     */
    private $bookingReferenceService;

    /**
     * @var TrainDataProvider
     */
    private $trainDataProvider;

    public function __construct(BookingReferenceProvider $bookingReferenceService, TrainDataProvider $trainDataProvider)
    {
        $this->bookingReferenceService = $bookingReferenceService;
        $this->trainDataProvider = $trainDataProvider;
    }

    public function makeReservation(ReservationRequest $reservationRequest): ReservationConfirmation
    {
        $trainIdentity = $reservationRequest->getTrainId();
        $trainTopology = $this->trainDataProvider->fetchTrainTopology($trainIdentity);

        $optionOfReservation = $trainTopology->tryToReserveSeats($reservationRequest->getNumberOfSeats());

        if (!$optionOfReservation->isSatisfied()) {
            return ReservationConfirmation::reject($trainIdentity);
        }

        $bookingReference = $this->bookingReferenceService->fetchNewBookingReference();
        $reservedSeats = $optionOfReservation->reserveSeatsWith($bookingReference);
        $this->trainDataProvider->markSeatsAsReserved($trainIdentity, $reservedSeats);

        return new ReservationConfirmation($reservationRequest->getTrainId(), $bookingReference, $reservedSeats);
    }
}
