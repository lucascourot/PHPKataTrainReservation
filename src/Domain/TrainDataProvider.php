<?php

namespace TrainReservation\Domain;

interface TrainDataProvider
{
    public function fetchTrainTopology(TrainId $trainId): TrainTopology;

    public function markSeatsAsReserved(TrainId $trainId, array $reservedSeats);
}
