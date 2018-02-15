<?php

namespace TrainReservation\Infrastructure\Adapters\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\Coach;
use TrainReservation\Domain\ReservedSeat;
use TrainReservation\Domain\Seat;
use TrainReservation\Domain\TrainDataProvider;
use TrainReservation\Domain\TrainId;
use TrainReservation\Domain\TrainTopology;

final class TrainDataProviderAdapter implements TrainDataProvider
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
     * Fetches and parses the json train topology provided by the train data provider
     */
    public function fetchTrainTopology(TrainId $trainId): TrainTopology
    {
        $response = $this->httpClient->send(new Request('GET', 'http://localhost:8081/data_for_train/'.$trainId->getId()));

        $seats = \GuzzleHttp\json_decode($response->getBody(), true);
        $topology = [];
        $coaches = [];

        foreach ($seats['seats'] as $seat) {
            $coaches[$seat['coach']][] = empty($seat['booking_reference'])
                ? new AvailableSeat($seat['seat_number'].$seat['coach'])
                : new ReservedSeat($seat['seat_number'].$seat['coach'], new BookingReference($seat['booking_reference']));
        }

        ksort($coaches);

        foreach ($coaches as $coach) {
            $seatsInCoach = array_values($coach);

            uasort($seatsInCoach, function (Seat $seat1, Seat $seat2) {
                return $seat1->getReference() <=> $seat2->getReference();
            });

            $topology[] = new Coach($seatsInCoach);
        }

        return new TrainTopology($topology);
    }

    /**
     * Sends an http request to the train data service to reserve chosen seats
     */
    public function markSeatsAsReservedFromList(TrainId $trainId, array $reservedSeats): void
    {
        $seatsReferences = [];
        $bookingReference = '';
        /** @var ReservedSeat $reservedSeat */
        foreach ($reservedSeats as $reservedSeat) {
            $bookingReference = $reservedSeat->getBookingReference()->getReference();
            $seatsReferences[] = $reservedSeat->getReference();
        }
        $seatsFormatted = '["'.implode('","', $seatsReferences).'"]';

        $this->httpClient->request(
            'POST',
            'http://localhost:8081/reserve',
            [
                'form_params' => [
                    'train_id' => $trainId->getId(),
                    'booking_reference' => $bookingReference,
                    'seats' => $seatsFormatted,
                ],
            ]
        );
    }
}
