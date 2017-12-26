<?php

namespace TrainReservation\Domain;

final class TrainId
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
            throw new \LogicException('Invalid train id.');
        }

        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
