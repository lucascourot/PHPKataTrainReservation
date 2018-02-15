<?php

namespace TrainReservation\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class Home
{
    public function index()
    {
        return new JsonResponse([
            'status' => 'working',
        ]);
    }
}
