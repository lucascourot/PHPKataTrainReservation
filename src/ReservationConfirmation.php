<?php

namespace TrainReservation;

final class ReservationConfirmation
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
     * @var ReservedSeat[]
     */
    private $seats;

    public function __construct(TrainId $trainId, BookingReference $bookingReference, array $seats)
    {
        $this->trainId = $trainId;
        $this->bookingReference = $bookingReference;
        $this->seats = $seats;
    }

    public static function reject(TrainId $trainId): self
    {
        return new self($trainId, BookingReference::empty(), []);
    }

    public function getTrainId(): TrainId
    {
        return $this->trainId;
    }

    public function getBookingReference(): BookingReference
    {
        return $this->bookingReference;
    }

    /**
     * @return ReservedSeat[]
     */
    public function getReservedSeats(): array
    {
        return $this->seats;
    }
}
