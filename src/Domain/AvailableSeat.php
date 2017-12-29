<?php

namespace TrainReservation\Domain;

final class AvailableSeat implements Seat
{
    /**
     * @var string
     */
    private $reference;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }

    public function reserveWith(BookingReference $bookingReference): ReservedSeat
    {
        return new ReservedSeat($this->reference, $bookingReference);
    }

    public function getReference(): string
    {
        return $this->reference;
    }
}
