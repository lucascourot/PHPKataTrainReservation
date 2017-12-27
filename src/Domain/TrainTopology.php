<?php

namespace TrainReservation\Domain;

final class TrainTopology
{
    private const OVERALL_TRAIN_CAPACITY_PERCENTAGE = 70;

    /**
     * @var Coach[]
     */
    private $coaches;

    /**
     * @var int
     */
    private $overallNumberOfAlreadyReservedSeats = 0;

    /**
     * @var int
     */
    private $overallNumberOfAllSeats;

    public function __construct(array $coaches)
    {
        $this->coaches = $coaches;

        foreach ($coaches as $coach) {
            foreach ($coach->getSeats() as $seat) {
                if ($seat instanceof ReservedSeat) {
                    ++$this->overallNumberOfAlreadyReservedSeats;
                }

                ++$this->overallNumberOfAllSeats;
            }
        }
    }

    /**
     * @param int $numberOfSeatsToReserve
     *
     * @return OptionOfReservation
     */
    public function tryToReserveSeats(int $numberOfSeatsToReserve): OptionOfReservation
    {
        $optionOfReservation = new OptionOfReservation($numberOfSeatsToReserve);

        if ($this->trainCapacityExceededWith($numberOfSeatsToReserve)) {
            return $optionOfReservation;
        }

        $singleCoachForReservation = $this->findACoachWithIdealCapacityFor($numberOfSeatsToReserve)
            ?? $this->findACoachThatCanBreakIdealCapacityFor($numberOfSeatsToReserve);

        if ($singleCoachForReservation) {
            $optionOfReservation->markSeatsAsReservedFromList(
                $singleCoachForReservation->getAvailableSeatsFor($numberOfSeatsToReserve)
            );

            return $optionOfReservation;
        }

        foreach ($this->coaches as $coach) {
            $optionOfReservation->markSeatsAsReservedFromList($coach->getAvailableSeatsFor($numberOfSeatsToReserve));

            if ($optionOfReservation->isSatisfied()) {
                break;
            }
        }

        return $optionOfReservation;
    }

    private function trainCapacityExceededWith(int $numberOfSeatsToReserve): bool
    {
        $reservedSeats = $this->overallNumberOfAlreadyReservedSeats + $numberOfSeatsToReserve;

        return $reservedSeats > $this->overallNumberOfAllSeats * self::OVERALL_TRAIN_CAPACITY_PERCENTAGE / 100;
    }

    private function findACoachWithIdealCapacityFor(int $numberOfSeatsToReserve): ?Coach
    {
        foreach ($this->coaches as $coach) {
            if (!$coach->exceedsIdealCapacityWith($numberOfSeatsToReserve)) {
                return $coach;
            }
        }

        return null;
    }

    private function findACoachThatCanBreakIdealCapacityFor(int $numberOfSeatsToReserve): ?Coach
    {
        foreach ($this->coaches as $coach) {
            if (count($coach->getAvailableSeatsFor($numberOfSeatsToReserve)) === $numberOfSeatsToReserve) {
                return $coach;
            }
        }

        return null;
    }
}
