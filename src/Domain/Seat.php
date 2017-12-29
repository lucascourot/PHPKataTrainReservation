<?php

namespace TrainReservation\Domain;

interface Seat
{
    public function getReference(): string;
}
