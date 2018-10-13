<?php

declare(strict_types=1);

namespace TrainReservation\Infrastructure\Http\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TrainReservation\Infrastructure\Http\PrimaryAdapters\HttpReserveSeatsAdapter;

class ReserveSeatsController
{
    /**
     * @var HttpReserveSeatsAdapter
     */
    private $reserveSeatsAdapter;

    public function __construct(HttpReserveSeatsAdapter $reserveSeatsAdapter)
    {
        $this->reserveSeatsAdapter = $reserveSeatsAdapter;
    }

    /**
     * Http controller to reserve seats
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->reserveSeatsAdapter->httpReserveSeats($request);
    }
}
