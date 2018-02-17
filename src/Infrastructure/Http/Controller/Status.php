<?php

namespace TrainReservation\Infrastructure\Http\Controller;

use Zend\Diactoros\Response\JsonResponse;

class Status
{
    public function ok()
    {
        return new JsonResponse([
            'status' => 'ok',
        ]);
    }
}
