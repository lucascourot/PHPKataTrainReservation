<?php

namespace TrainReservation;

final class TrainTopology
{
    /**
     * @var Coach[]
     */
    private $coaches;

    public function __construct(array $coaches)
    {
        $this->coaches = $coaches;
    }

    /**
     * @return Coach[]
     */
    public function getCoaches(): array
    {
        return $this->coaches;
    }
}
