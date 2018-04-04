<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

final class TicketOffice implements MakesReservation
{
    /**
     * @var ProvidesBookingReference
     */
    private $bookingReferenceService;

    /**
     * @var ProvidesTrainData
     */
    private $trainDataProvider;

    public function __construct(ProvidesBookingReference $bookingReferenceService, ProvidesTrainData $trainDataProvider)
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
        $this->trainDataProvider->markSeatsAsReservedFromList($trainIdentity, $reservedSeats);

        return new ReservationConfirmation($reservationRequest->getTrainId(), $bookingReference, $reservedSeats);
    }
}
