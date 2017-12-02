<?php

namespace TrainReservation;

final class Coach
{
    /**
     * @var Seat[]
     */
    private $seats;

    public function __construct(array $seats)
    {
        $this->seats = $seats;
    }

    /**
     * @return Seat[]
     */
    public function getSeats(): array
    {
        return $this->seats;
    }

    public function getNumberOfAlreadyReservedSeats(): int
    {
        $numberOfAlreadyReservedSeats = 0;

        foreach ($this->getSeats() as $seat) {
            if ($seat instanceof ReservedSeat) {
                ++$numberOfAlreadyReservedSeats;
            }
        }

        return $numberOfAlreadyReservedSeats;
    }
}
