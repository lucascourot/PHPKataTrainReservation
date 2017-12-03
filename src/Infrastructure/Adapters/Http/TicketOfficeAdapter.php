<?php

namespace TrainReservation\Infrastructure\Adapters\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TrainReservation\Domain\BookingReferenceProvider;
use TrainReservation\Domain\ReservationRequest;
use TrainReservation\Domain\TicketOffice;
use TrainReservation\Domain\TrainDataProvider;
use TrainReservation\Domain\TrainId;
use Zend\Diactoros\Response\JsonResponse;

class TicketOfficeAdapter
{
    /**
     * @var BookingReferenceProvider
     */
    private $bookingReferenceService;

    /**
     * @var TrainDataProvider
     */
    private $trainDataProvider;

    public function __construct(BookingReferenceProvider $bookingReferenceService, TrainDataProvider $trainDataProvider)
    {
        $this->bookingReferenceService = $bookingReferenceService;
        $this->trainDataProvider = $trainDataProvider;
    }

    public function reserveSeats(ServerRequestInterface $request): ResponseInterface
    {
        $ticketOffice = new TicketOffice($this->bookingReferenceService, $this->trainDataProvider);

        $confirmation = $ticketOffice->makeReservation(new ReservationRequest(
            new TrainId($request->getParsedBody()['train_id']),
            $request->getParsedBody()['number_of_seats']
        ));

        $reservedSeatsPresentation = [];
        foreach ($confirmation->getReservedSeats() as $reservedSeat) {
            $reservedSeatsPresentation[$reservedSeat->getReference()] = $reservedSeat->getBookingReference()->getReference();
        }

        return new JsonResponse([
            'train_id' => $confirmation->getTrainId()->getId(),
            'booking_reference' => $confirmation->getBookingReference()->getReference(),
            'seats' => $reservedSeatsPresentation,
        ]);
    }
}
