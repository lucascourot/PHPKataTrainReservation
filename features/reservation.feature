Feature: Reserve seats
  In order to satisfy travelers
  As a railway operator
  I need to be able to correctly place travelers in the coaches

  Rules:
  - For a train overall, no more than 70% of seats may be reserved
  - Ideally no individual coach should have no more than 70% reserved seats
  - For one reservation, all the seats must be in the same coach, even if this makes me go over 70% for the coach

  Background:
    Given I'm doing a reservation on the train named "express_2000"
    And I'm doing a reservation under booking reference "75bcd15" provided by the booking reference service

  Scenario: Reserve a seat when train is empty
    Given train topology below:
      | coach | total | reserved |
      | A     | 5     | 0        |
    When I reserve 1 seat
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | A     | 1           |

  Scenario: Reserve seats when train is empty
    Given train topology below:
      | coach | total | reserved |
      | A     | 10    | 0        |
    When I reserve 3 seats
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | A     | 1           |
      | A     | 2           |
      | A     | 3           |

  Scenario: Reserve one seat minimum
    Given train topology below:
      | coach | total | reserved |
      | A     | 10    | 0        |
    When I reserve 0 seat
    Then I should get an error message "Cannot reserve less than one seat."

  Scenario: Do no exceed train overall capacity of 70 percent
    Given train topology below:
      | coach | total | reserved |
      | A     | 10    | 7        |
    When I reserve 1 seat
    Then reservation should be rejected

  Scenario: Do no ideally exceed individual coach capacity of 70 percent
    Given train topology below:
      | coach | total | reserved |
      | A     | 10    | 7        |
      | B     | 10    | 5        |
    When I reserve 1 seat
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | B     | 6           |

  Scenario: Should break individual coach capacity of 70 percent if no alternative
    Given train topology below:
      | coach | total | reserved |
      | A     | 10    | 7        |
      | B     | 10    | 7        |
      | C     | 10    | 5        |
      | D     | 10    | 6        |
      | E     | 10    | 6        |
    When I reserve 4 seats
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | C     | 6           |
      | C     | 7           |
      | C     | 8           |
      | C     | 9           |

  Scenario: Should reserve seats in the same coach for the same reservation
    Given train topology below:
      | coach | total | reserved |
      | A     | 10    | 6        |
      | B     | 10    | 5        |
    When I reserve 2 seats
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | B     | 6           |
      | B     | 7           |

  Scenario: Should reserve seats in different coaches for the same reservation if no alternative
    Given train topology below:
      | coach | total | reserved |
      | A     | 10    | 5        |
      | B     | 10    | 5        |
      | C     | 10    | 5        |
    When I reserve 6 seats
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | A     | 6           |
      | A     | 7           |
      | A     | 8           |
      | A     | 9           |
      | A     | 10          |
      | B     | 6           |
