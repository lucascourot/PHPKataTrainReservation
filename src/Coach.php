<?php

namespace TrainReservation;

final class Coach
{
    private const IDEAL_CAPACITY_PERCENTAGE = 70;

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

    public function exceedsIdealCapacityWith(int $numberOfSeatsToReserve): bool
    {
        $reservedSeats = $this->getNumberOfAlreadyReservedSeats() + $numberOfSeatsToReserve;

        return $reservedSeats > count($this->getSeats()) * self::IDEAL_CAPACITY_PERCENTAGE / 100;
    }

    /**
     * @return AvailableSeat[]
     */
    public function getAvailableSeatsFor(int $numberOfSeatsToReserve): array
    {
        $availableSeats = [];

        foreach ($this->seats as $seat) {
            if ($seat instanceof AvailableSeat) {
                $availableSeats[] = $seat;
            }
        }

        return array_slice($availableSeats, 0, $numberOfSeatsToReserve);
    }
}
