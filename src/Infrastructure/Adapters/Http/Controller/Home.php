<?php

namespace TrainReservation\Infrastructure\Adapters\Http\Controller;

use Zend\Diactoros\Response\JsonResponse;

class Home
{
    public function ok()
    {
        return new JsonResponse([
            'status' => 'working',
        ]);
    }
}
