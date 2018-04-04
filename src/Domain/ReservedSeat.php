<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

final class ReservedSeat implements Seat
{
    /**
     * @var string
     */
    private $reference;

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

    public function getReference(): string
    {
        return $this->reference;
    }
}
