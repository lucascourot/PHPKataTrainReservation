<?php

namespace TrainReservation\Domain;

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

    public function markSeatsAsReservedFromList(array $seats): void
    {
        if ($this->isSatisfied()) {
            return;
        }

        foreach ($seats as $availableSeat) {
            if (!$availableSeat instanceof AvailableSeat) {
                continue;
            }

            $this->seatsToReserve[] = $availableSeat;

            if ($this->isSatisfied()) {
                break;
            }
        }
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
        if (!$this->isSatisfied()) {
            throw new \LogicException('Cannot reserve seats, the option of reservation has not been satisfied.');
        }

        $reservedSeats = [];

        foreach ($this->seatsToReserve as $availableSeat) {
            $reservedSeats[] = $availableSeat->reserveWith($bookingReference);
        }

        return $reservedSeats;
    }
}
