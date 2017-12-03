<?php

namespace TrainReservation\Domain;

final class BookingReference
{
    /**
     * @var string
     */
    private $reference;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }

    public static function empty(): self
    {
        return new self('');
    }

    public function getReference(): string
    {
        return $this->reference;
    }
}
