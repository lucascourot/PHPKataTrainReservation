<?php

namespace TrainReservation;

interface TrainDataProvider
{
    public function fetchTrainTopology(TrainId $trainId): TrainTopology;

    public function markSeatsAsReserved(TrainId $trainId, array $reservedSeats);
}
