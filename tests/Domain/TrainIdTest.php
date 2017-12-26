<?php

namespace TrainReservationTest\Domain;

use PHPUnit\Framework\TestCase;
use TrainReservation\Domain\TrainId;

class TrainIdTest extends TestCase
{
    public function testShouldBeAlphanum()
    {
        // When
        $trainId = new TrainId('express2000');

        // Then
        $this->assertSame('express2000', $trainId->getId());
    }

    public function testShouldNotContainSpecialChars()
    {
        // Expect
        $this->expectException(\LogicException::class);

        // When
        new TrainId('expre$$');
    }
}
