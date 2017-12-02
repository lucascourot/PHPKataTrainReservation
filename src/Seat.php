<?php

namespace TrainReservation;

abstract class Seat
{
    /**
     * @var string
     */
    protected $reference;

    public function getReference(): string
    {
        return $this->reference;
    }
}
