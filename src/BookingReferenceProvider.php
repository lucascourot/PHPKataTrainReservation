<?php

namespace TrainReservation;

interface BookingReferenceProvider
{
    public function fetchNewBookingReference(): BookingReference;
}
