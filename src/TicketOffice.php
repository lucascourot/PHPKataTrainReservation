<?php

namespace TrainReservation;

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
        $bookingReference = $this->bookingReferenceService->fetchNewBookingReference();
        $trainTopology = $this->trainDataProvider->fetchTrainTopology($reservationRequest->getTrainId());

        $reservedSeats = [];

        foreach ($trainTopology->getCoaches() as $coach) {
            foreach ($coach->getSeats() as $seat) {
                if ($seat instanceof AvailableSeat) {
                    $reservedSeats[] = $seat->reserve();
                }

                if ($reservationRequest->getNumberOfSeats() === count($reservedSeats)) {
                    break 2;
                }
            }
        }

        if (empty($reservedSeats)) {
            return ReservationConfirmation::reject($reservationRequest->getTrainId());
        }

        $this->trainDataProvider->markSeatsAsReserved($reservationRequest->getTrainId(), $reservedSeats);

        return new ReservationConfirmation($reservationRequest->getTrainId(), $bookingReference, $reservedSeats);
    }
}
