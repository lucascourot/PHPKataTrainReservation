<?php

namespace TrainReservation;

final class ReservationRequest
{
    /**
     * @var TrainId
     */
    private $trainId;

    /**
     * @var int
     */
    private $numberOfSeats;

    public function __construct(TrainId $trainId, int $numberOfSeats)
    {
        $this->trainId = $trainId;
        $this->numberOfSeats = $numberOfSeats;
    }

    public function getTrainId(): TrainId
    {
        return $this->trainId;
    }

    public function getNumberOfSeats(): int
    {
        return $this->numberOfSeats;
    }
}
