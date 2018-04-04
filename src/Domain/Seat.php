<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

interface Seat
{
    public function getReference(): string;
}
