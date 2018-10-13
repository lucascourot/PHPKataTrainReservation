<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

interface ReserveSeats
{
    public function reserveSeats(string $trainId, int $nbSeats): ReservationConfirmation;
}
