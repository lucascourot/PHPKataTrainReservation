<?php

declare(strict_types=1);

namespace TrainReservation\Infrastructure\Http\PrimaryAdapters;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TrainReservation\Domain\MakesReservation;
use TrainReservation\Domain\ReservationRequest;
use TrainReservation\Domain\TrainId;
use Zend\Diactoros\Response\JsonResponse;

class MakeReservation
{
    /**
     * @var MakesReservation
     */
    private $makesReservation;

    public function __construct(MakesReservation $makesReservation)
    {
        $this->makesReservation = $makesReservation;
    }

    /**
     * Http controller to reserve seats
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $requestSeatCount = $request->getParsedBody()['seat_count'];
        if (!is_numeric($requestSeatCount)) {
            throw new \InvalidArgumentException('seat_count should be a numeric value.');
        }
        $requestTrainId = $request->getParsedBody()['train_id'];

        $confirmation = $this->makesReservation->makeReservation(
            new ReservationRequest(new TrainId($requestTrainId), (int) $requestSeatCount)
        );

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
