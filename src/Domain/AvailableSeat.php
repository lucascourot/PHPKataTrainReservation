<?php

namespace TrainReservation\Domain;

final class AvailableSeat extends Seat
{
    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }

    public function reserveWith(BookingReference $bookingReference): ReservedSeat
    {
        return new ReservedSeat($this->reference, $bookingReference);
    }
}
