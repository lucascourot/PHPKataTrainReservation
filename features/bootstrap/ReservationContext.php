<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use TrainReservation\Domain\AvailableSeat;
use TrainReservation\Domain\BookingReference;
use TrainReservation\Domain\BookingReferenceProvider;
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
    public function imDoingAReservationUnderBookingReferenceProvidedByTheBookingReferenceService(string $bookingReference) {
        $this->bookingReference = new BookingReference($bookingReference);

        $this->bookingReferenceProvider->allows([
            'fetchNewBookingReference' => $this->bookingReference
        ]);
    }

    /**
     * @Given the train is empty
     */
    public function theTrainIsEmpty()
    {
        $trainTopology = new TrainTopology([
            new \TrainReservation\Domain\Coach([
                new AvailableSeat('A1'),
                new AvailableSeat('A2'),
                new AvailableSeat('A3'),
                new AvailableSeat('A4'),
                new AvailableSeat('A5'),
                new AvailableSeat('A6'),
                new AvailableSeat('A7'),
                new AvailableSeat('A8'),
                new AvailableSeat('A9'),
                new AvailableSeat('A10'),
            ])
        ]);

        $this->trainDataProvider->shouldReceive('fetchTrainTopology')->withArgs([$this->trainId])->andReturn($trainTopology);
    }

    /**
     * @When I reserve :numberOfSeats seats
     */
    public function iReserveSeats(int $numberOfSeats)
    {
        $ticketOffice = new TicketOffice($this->bookingReferenceProvider, $this->trainDataProvider);
        $this->reservationConfirmation = $ticketOffice->makeReservation(new ReservationRequest($this->trainId, $numberOfSeats));
    }

    /**
     * @Then seats :seats should be marked as reserved
     */
    public function seatsShouldBeMarkedAsReservedUnderBookingReferenceForTrain($seats)
    {
        $this->trainDataProvider->shouldHaveReceived('markSeatsAsReservedFromList', [
            $this->trainId,
            [
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
            ]
        ]);

        Assert::assertEquals($this->trainId, $this->reservationConfirmation->getTrainId());
        Assert::assertEquals($this->bookingReference, $this->reservationConfirmation->getBookingReference());
        Assert::assertEquals(
            [
                new ReservedSeat('A1', $this->bookingReference),
                new ReservedSeat('A2', $this->bookingReference),
                new ReservedSeat('A3', $this->bookingReference),
            ],
            $this->reservationConfirmation->getReservedSeats()
        );
    }
}
