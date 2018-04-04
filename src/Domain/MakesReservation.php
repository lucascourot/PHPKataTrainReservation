<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

interface MakesReservation
{
    public function makeReservation(ReservationRequest $reservationRequest): ReservationConfirmation;
}
