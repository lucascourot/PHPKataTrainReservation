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

  Scenario: Reserve seats when train is empty
    Given the train with 10 seats is empty
    When I reserve 3 seats
    Then seats below should be marked as reserved:
      | coach | seat |
      | A     | 1    |
      | A     | 2    |
      | A     | 3    |

  Scenario: Reserve at least one seat
    Given the train with 10 seats is empty
    When I reserve 0 seat
    Then reservation confirmation should be empty
