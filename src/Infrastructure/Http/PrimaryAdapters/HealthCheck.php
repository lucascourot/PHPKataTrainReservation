<?php

declare(strict_types=1);

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
