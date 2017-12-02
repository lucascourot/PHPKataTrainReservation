<?php

namespace TrainReservation;

final class OptionOfReservation
{
    /**
     * @var int
     */
    private $numberOfSeatsToReserve;

    /**
     * @var AvailableSeat[]
     */
    private $seatsToReserve = [];

    public function __construct(int $numberOfSeatsToReserve)
    {
        $this->numberOfSeatsToReserve = $numberOfSeatsToReserve;
    }

    public function markSeatAsResearved(AvailableSeat $availableSeat): void
    {
        $this->seatsToReserve[] = $availableSeat;
    }

    public function isSatisfied(): bool
    {
        return count($this->seatsToReserve) === $this->numberOfSeatsToReserve;
    }

    /**
     * @return ReservedSeat[]
     */
    public function reserveSeatsWith(BookingReference $bookingReference): array
    {
        $reservedSeats = [];

        foreach ($this->seatsToReserve as $availableSeat) {
            $reservedSeats[] = $availableSeat->reserveWith($bookingReference);
        }

        return $reservedSeats;
    }
}
