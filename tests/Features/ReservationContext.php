<?php

namespace TrainReservationTest\Features;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Mockery;
use PHPUnit\Framework\Assert;
use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\BookingReferenceProvider;
use TrainReservation\Domain\Coach;
use TrainReservation\Domain\ReservationConfirmation;
use TrainReservation\Domain\ReservationRequest;
use TrainReservation\Domain\ReservedSeat;
use TrainReservation\Domain\TicketOffice;
use TrainReservation\Domain\TrainDataProvider;
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
     * @var BookingReferenceProvider
     */
    private $bookingReferenceProvider;

    /**
     * @var TrainDataProvider
     */
    private $trainDataProvider;

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
        $this->bookingReferenceProvider = Mockery::mock(BookingReferenceProvider::class);
        $this->trainDataProvider = Mockery::spy(TrainDataProvider::class);
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

        $this->bookingReferenceProvider->allows([
            'fetchNewBookingReference' => $this->bookingReference,
        ]);
    }

    /**
     * @When I reserve :numberOfSeats seat(s)
     */
    public function iReserveSeats(int $numberOfSeats)
    {
        try {
            $ticketOffice = new TicketOffice($this->bookingReferenceProvider, $this->trainDataProvider);
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

        $this->trainDataProvider->shouldHaveReceived('markSeatsAsReservedFromList', [$this->trainId, $seats]);
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
            $coaches[$seat['coach']][] = empty($seat['reserved'])
                ? new AvailableSeat($seat['seat_number'].$seat['coach'])
                : new ReservedSeat($seat['seat_number'].$seat['coach'], new BookingReference('abcde'));
        }

        foreach ($coaches as $coach) {
            $seatsInCoach = array_values($coach);

            $topology[] = new Coach($seatsInCoach);
        }

        $trainTopology = new TrainTopology($topology);

        $this->trainDataProvider->shouldReceive('fetchTrainTopology')->withArgs([$this->trainId])->andReturn($trainTopology);
    }

    /**
     * @Then reservation should be rejected
     */
    public function reservationShouldBeRejected()
    {
        $this->trainDataProvider->shouldNotReceive('markSeatsAsReservedFromList');

        Assert::assertEquals($this->trainId, $this->reservationConfirmation->getTrainId());
        Assert::assertEquals(BookingReference::empty(), $this->reservationConfirmation->getBookingReference());
        Assert::assertEquals([], $this->reservationConfirmation->getReservedSeats());
    }
}
