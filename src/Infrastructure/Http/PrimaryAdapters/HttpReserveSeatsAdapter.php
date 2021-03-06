<?php

declare(strict_types=1);

namespace TrainReservation\Infrastructure\Http\PrimaryAdapters;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TrainReservation\Domain\ReserveSeats;
use Zend\Diactoros\Response\JsonResponse;

class HttpReserveSeatsAdapter
{
    /**
     * @var ReserveSeats
     */
    private $hexagon;

    public function __construct(ReserveSeats $reserveSeatsApi)
    {
        $this->hexagon = $reserveSeatsApi;
    }

    /**
     * Http controller to reserve seats
     */
    public function httpReserveSeats(ServerRequestInterface $request): ResponseInterface
    {
        $requestSeatCount = $request->getParsedBody()['seat_count'];
        if (!is_numeric($requestSeatCount)) {
            throw new \InvalidArgumentException('seat_count should be a numeric value.');
        }
        $requestTrainId = $request->getParsedBody()['train_id'];

        $confirmation = $this->hexagon->reserveSeats($requestTrainId, (int) $requestSeatCount);

        $reservedSeatsPresentation = [];
        foreach ($confirmation->getReservedSeats() as $reservedSeat) {
            $reservedSeatsPresentation[] = $reservedSeat->getReference();
        }

        return new JsonResponse([
            'train_id' => $confirmation->getTrainId()->getId(),
            'booking_reference' => $confirmation->getBookingReference()->getReference(),
            'seats' => $reservedSeatsPresentation,
        ]);
    }
}
