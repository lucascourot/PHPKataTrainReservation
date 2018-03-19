<?php

namespace TrainReservation\Domain;

interface ProvidesBookingReference
{
    public function fetchNewBookingReference(): BookingReference;
}
