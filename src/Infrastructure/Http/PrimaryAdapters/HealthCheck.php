<?php

namespace TrainReservation\Infrastructure\Http\PrimaryAdapters;

use Zend\Diactoros\Response\JsonResponse;

class HealthCheck
{
    public function __invoke()
    {
        return new JsonResponse([
            'status' => 'ok',
        ]);
    }
}
