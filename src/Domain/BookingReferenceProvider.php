<?php

namespace TrainReservation\Domain;

interface BookingReferenceProvider
{
    public function fetchNewBookingReference(): BookingReference;
}
