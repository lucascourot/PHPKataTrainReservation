<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

interface ProvidesTrainData
{
    public function fetchTrainTopology(TrainId $trainId): TrainTopology;

    /**
     * @param ReservedSeat[] $reservedSeats
     */
    public function markSeatsAsReservedFromList(TrainId $trainId, array $reservedSeats): void;
}
