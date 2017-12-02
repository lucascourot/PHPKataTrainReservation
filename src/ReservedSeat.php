<?php

namespace TrainReservation;

final class ReservedSeat extends Seat
{
    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }
}
