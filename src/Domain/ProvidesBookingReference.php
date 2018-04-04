<?php

declare(strict_types=1);

namespace TrainReservation\Domain;

interface ProvidesBookingReference
{
    public function fetchNewBookingReference(): BookingReference;
}
