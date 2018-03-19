<?php

namespace TrainReservation\Domain;

interface MakesReservation
{
    public function makeReservation(ReservationRequest $reservationRequest): ReservationConfirmation;
}
