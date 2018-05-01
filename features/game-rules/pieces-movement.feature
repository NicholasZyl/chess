Feature: Pieces movement
  In order to play the chess
  As a player
  I need to move my pieces

  Scenario: King's legal move
    Given there is a chessboard with "White king" placed on d4
    When I move piece from d4 to e5
    Then "White king" should be placed on e5

  Scenario: King's illegal move
    Given there is a chessboard with "White king" placed on D4
    When I move piece from d4 to f8
    Then the move is illegal
    And "White king" should still be placed on d4

  Scenario: Queen's legal move
    Given there is a chessboard with "White queen" placed on d4
    When I move piece from d4 to g1
    Then "White queen" should be placed on g1

  Scenario: Queen's illegal move
    Given there is a chessboard with "White queen" placed on d4
    When I move piece from d4 to g2
    Then the move is illegal
    And "White queen" should still be placed on d4