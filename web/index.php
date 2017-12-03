<?php

require __DIR__.'/../vendor/autoload.php';

use TrainReservation\Infrastructure\Adapters\Http\BookingReferenceProviderAdapter;
use TrainReservation\Infrastructure\Adapters\Http\TicketOfficeAdapter;
use TrainReservation\Infrastructure\Adapters\Http\TrainDataProviderAdapter;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Server;

$guzzle = new \GuzzleHttp\Client();
$ticketOffice = new TicketOfficeAdapter(new BookingReferenceProviderAdapter($guzzle), new TrainDataProviderAdapter($guzzle));

$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST);

$server = Server::createServerFromRequest(
    function (\Zend\Diactoros\ServerRequest $request, $response, $done) use ($ticketOffice) {
        if ($request->getMethod() === 'POST') {
            return $ticketOffice->reserveSeats($request);
        }

        return new \Zend\Diactoros\Response\JsonResponse(['status' => 'working']);
    },
    $request
);

$server->listen();
