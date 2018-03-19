<?php

namespace TrainReservationTest\Features;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Mockery;
use PHPUnit\Framework\Assert;
use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\Coach;
use TrainReservation\Domain\ProvidesBookingReference;
use TrainReservation\Domain\ProvidesTrainData;
use TrainReservation\Domain\ReservationConfirmation;
use TrainReservation\Domain\ReservationRequest;
use TrainReservation\Domain\ReservedSeat;
use TrainReservation\Domain\TicketOffice;
use TrainReservation\Domain\TrainId;
use TrainReservation\Domain\TrainTopology;

/**
 * Defines application features from the specific context.
 */
class ReservationContext implements Context
{
    /**
     * @var TrainId
     */
    private $trainId;

    /**
     * @var BookingReference
     */
    private $bookingReference;

    /**
     * @var ProvidesBookingReference
     */
    private $providesBookingReference;

    /**
     * @var ProvidesTrainData
     */
    private $providesTrainData;

    /**
     * @var ReservationConfirmation
     */
    private $reservationConfirmation;

    /**
     * @var \Exception
     */
    private $reservationException;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->providesBookingReference = Mockery::mock(ProvidesBookingReference::class);
        $this->providesTrainData = Mockery::spy(ProvidesTrainData::class);
    }

    /**
     * @Given I'm doing a reservation on the train named :trainId
     */
    public function thereIsATrainNamed(string $trainId)
    {
        $this->trainId = new TrainId($trainId);
    }

    /**
     * @Given I'm doing a reservation under booking reference :bookingReference provided by the booking reference service
     */
    public function imDoingAReservationUnderBookingReferenceProvidedByTheBookingReferenceService(string $bookingReference)
    {
        $this->bookingReference = new BookingReference($bookingReference);

        $this->providesBookingReference->allows([
            'fetchNewBookingReference' => $this->bookingReference,
        ]);
    }

    /**
     * @When I reserve :numberOfSeats seat(s)
     */
    public function iReserveSeats(int $numberOfSeats)
    {
        try {
            $ticketOffice = new TicketOffice($this->providesBookingReference, $this->providesTrainData);
            $this->reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, $numberOfSeats));
        } catch (\Exception $exception) {
            $this->reservationException = $exception;
        }
    }

    /**
     * @Then seats below should be marked as reserved:
     */
    public function seatsBelowShouldBeMarkedAsReserved(TableNode $table)
    {
        $seats = [];

        foreach ($table as $row) {
            $seats[] = new ReservedSeat($row['seat_number'].$row['coach'], $this->bookingReference);
        }

        $this->providesTrainData->shouldHaveReceived('markSeatsAsReservedFromList', [$this->trainId, $seats]);
        Assert::assertEmpty($this->reservationException);

        Assert::assertEquals($this->trainId, $this->reservationConfirmation->getTrainId());
        Assert::assertEquals($this->bookingReference, $this->reservationConfirmation->getBookingReference());
        Assert::assertEquals($seats, $this->reservationConfirmation->getReservedSeats());
    }

    /**
     * @Then I should get an error message :message
     */
    public function iShouldGetAnErrorMessage($message)
    {
        Assert::assertInstanceOf(\LogicException::class, $this->reservationException);
        Assert::assertSame($message, $this->reservationException->getMessage());
        Assert::assertEmpty($this->reservationConfirmation);
    }

    /**
     * @Given train topology below:
     */
    public function trainTopologyBelow(TableNode $seats)
    {
        $topology = [];
        $coaches = [];

        foreach ($seats as $seat) {
            $total = (int) $seat['total'];
            $reserved = (int) $seat['reserved'];
            $seatNumber = 1;

            while ($seatNumber <= $total) {
                while ($reserved-- > 0) {
                    $coaches[$seat['coach']][] = new ReservedSeat($seatNumber++.$seat['coach'], new BookingReference('abcde'));
                }

                $coaches[$seat['coach']][] = new AvailableSeat($seatNumber++.$seat['coach']);
            }
        }

        foreach ($coaches as $coach) {
            $seatsInCoach = array_values($coach);

            $topology[] = new Coach($seatsInCoach);
        }

        $trainTopology = new TrainTopology($topology);

        $this->providesTrainData->shouldReceive('fetchTrainTopology')->withArgs([$this->trainId])->andReturn($trainTopology);
    }

    /**
     * @Then reservation should be rejected
     */
    public function reservationShouldBeRejected()
    {
        $this->providesTrainData->shouldNotReceive('markSeatsAsReservedFromList');

        Assert::assertEquals($this->trainId, $this->reservationConfirmation->getTrainId());
        Assert::assertEquals(BookingReference::empty(), $this->reservationConfirmation->getBookingReference());
        Assert::assertEquals([], $this->reservationConfirmation->getReservedSeats());
    }
}
