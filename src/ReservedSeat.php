<?php

namespace TrainReservation;

final class ReservedSeat extends Seat
{
    /**
     * @var BookingReference
     */
    private $bookingReference;

    public function __construct(string $reference, BookingReference $bookingReference)
    {
        $this->reference = $reference;
        $this->bookingReference = $bookingReference;
    }

    public function getBookingReference(): BookingReference
    {
        return $this->bookingReference;
    }
}
