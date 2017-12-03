<?php

namespace TrainReservation\Domain;

interface TrainDataProvider
{
    public function fetchTrainTopology(TrainId $trainId): TrainTopology;

    /**
     * @param ReservedSeat[] $reservedSeats
     */
    public function markSeatsAsReserved(TrainId $trainId, array $reservedSeats): void;
}
