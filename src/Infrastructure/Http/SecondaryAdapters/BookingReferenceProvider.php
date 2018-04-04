<?php

declare(strict_types=1);

namespace TrainReservation\Infrastructure\Http\SecondaryAdapters;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\ProvidesBookingReference;

final class BookingReferenceProvider implements ProvidesBookingReference
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Requests the booking service to fetch a new booking reference
     */
    public function fetchNewBookingReference(): BookingReference
    {
        $response = $this->httpClient->send(new Request('GET', 'http://localhost:8082/booking_reference'));

        return new BookingReference($response->getBody()->getContents());
    }
}
