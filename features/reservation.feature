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
      | coach | seat_number | reserved |
      | A     | 1           |          |
      | A     | 2           |          |
      | A     | 3           |          |
      | A     | 4           |          |
      | A     | 5           |          |
    When I reserve 1 seat
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | A     | 1           |

  Scenario: Reserve seats when train is empty
    Given train topology below:
      | coach | seat_number | reserved |
      | A     | 1           |          |
      | A     | 2           |          |
      | A     | 3           |          |
      | A     | 4           |          |
      | A     | 5           |          |
      | A     | 6           |          |
      | A     | 7           |          |
      | A     | 8           |          |
      | A     | 9           |          |
      | A     | 10          |          |
    When I reserve 3 seats
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | A     | 1           |
      | A     | 2           |
      | A     | 3           |

  Scenario: Reserve one seat minimum
    Given train topology below:
      | coach | seat_number | reserved |
      | A     | 1           |          |
      | A     | 2           |          |
      | A     | 3           |          |
      | A     | 4           |          |
      | A     | 5           |          |
      | A     | 6           |          |
      | A     | 7           |          |
      | A     | 8           |          |
      | A     | 9           |          |
      | A     | 10          |          |
    When I reserve 0 seat
    Then I should get an error message "Cannot reserve less than one seat."

  Scenario: Do no exceed train overall capacity of 70 percent
    Given train topology below:
      | coach | seat_number | reserved |
      | A     | 1           | x        |
      | A     | 2           | x        |
      | A     | 3           | x        |
      | A     | 4           | x        |
      | A     | 5           | x        |
      | A     | 6           | x        |
      | A     | 7           | x        |
      | A     | 8           |          |
      | A     | 9           |          |
      | A     | 10          |          |
    When I reserve 1 seat
    Then reservation should be rejected

  Scenario: Do no ideally exceed individual coach capacity of 70 percent
    Given train topology below:
      | coach | seat_number | reserved |
      | A     | 1           | x        |
      | A     | 2           | x        |
      | A     | 3           | x        |
      | A     | 4           | x        |
      | A     | 5           | x        |
      | A     | 6           | x        |
      | A     | 7           | x        |
      | A     | 8           |          |
      | A     | 9           |          |
      | A     | 10          |          |

      | B     | 1           | x        |
      | B     | 2           | x        |
      | B     | 3           | x        |
      | B     | 4           | x        |
      | B     | 5           | x        |
      | B     | 6           |          |
      | B     | 7           |          |
      | B     | 8           |          |
      | B     | 9           |          |
      | B     | 10          |          |
    When I reserve 1 seat
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | B     | 6           |

  Scenario: Should break individual coach capacity of 70 percent if no alternative
    Given train topology below:
      | coach | seat_number | reserved |
      | A     | 1           | x        |
      | A     | 2           | x        |
      | A     | 3           | x        |
      | A     | 4           | x        |
      | A     | 5           | x        |
      | A     | 6           | x        |
      | A     | 7           | x        |
      | A     | 8           |          |
      | A     | 9           |          |
      | A     | 10          |          |

      | B     | 1           | x        |
      | B     | 2           | x        |
      | B     | 3           | x        |
      | B     | 4           | x        |
      | B     | 5           | x        |
      | B     | 6           | x        |
      | B     | 7           | x        |
      | B     | 8           |          |
      | B     | 9           |          |
      | B     | 10          |          |

      | C     | 1           | x        |
      | C     | 2           | x        |
      | C     | 3           | x        |
      | C     | 4           | x        |
      | C     | 5           | x        |
      | C     | 6           |          |
      | C     | 7           |          |
      | C     | 8           |          |
      | C     | 9           |          |
      | C     | 10          |          |

      | D     | 1           | x        |
      | D     | 2           | x        |
      | D     | 3           | x        |
      | D     | 4           | x        |
      | D     | 5           | x        |
      | D     | 6           | x        |
      | D     | 7           |          |
      | D     | 8           |          |
      | D     | 9           |          |
      | D     | 10          |          |

      | E     | 1           | x        |
      | E     | 2           | x        |
      | E     | 3           | x        |
      | E     | 4           | x        |
      | E     | 5           | x        |
      | E     | 6           | x        |
      | E     | 7           |          |
      | E     | 8           |          |
      | E     | 9           |          |
      | E     | 10          |          |
    When I reserve 4 seats
    Then seats below should be marked as reserved:
      | coach | seat_number |
      | C     | 6           |
      | C     | 7           |
      | C     | 8           |
      | C     | 9           |
