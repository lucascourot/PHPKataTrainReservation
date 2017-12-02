<?php

namespace TrainReservation;

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
    private $overallNumberOfAlreadyReservedSeats;

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

        foreach ($this->coaches as $coach) {
            foreach ($coach->getSeats() as $seat) {
                $numberOfSeatsToReserveInCoach = 0;

                if ($seat instanceof AvailableSeat) {
                    if ($coach->exceedsIdealCapacityWith(++$numberOfSeatsToReserveInCoach)) {
                        continue;
                    }

                    $optionOfReservation->markSeatAsResearved($seat);

                    if ($optionOfReservation->isSatisfied()) {
                        break 2;
                    }
                }
            }
        }

        return $optionOfReservation;
    }

    /**
     * @param int $numberOfSeatsToReserve
     *
     * @return bool
     */
    private function trainCapacityExceededWith(int $numberOfSeatsToReserve): bool
    {
        $reservedSeats = $this->overallNumberOfAlreadyReservedSeats + $numberOfSeatsToReserve;

        return $reservedSeats > $this->overallNumberOfAllSeats * self::OVERALL_TRAIN_CAPACITY_PERCENTAGE / 100;
    }
}
