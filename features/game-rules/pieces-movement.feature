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

  Scenario: Rook's legal move
    Given there is a chessboard with "White rook" placed on d4
    When I move piece from d4 to d8
    Then "White rook" should be placed on d8

  Scenario: Rook's illegal move
    Given there is a chessboard with "White rook" placed on d4
    When I move piece from d4 to e5
    Then the move is illegal
    And "White rook" should still be placed on d4

  Scenario: Bishop's legal move
    Given there is a chessboard with "White bishop" placed on d4
    When I move piece from d4 to g7
    Then "White bishop" should be placed on g7

  Scenario: Bishop's illegal move
    Given there is a chessboard with "White bishop" placed on d4
    When I move piece from d4 to d5
    Then the move is illegal
    And "White bishop" should still be placed on d4

  Scenario: Knight's legal move
    Given there is a chessboard with "White knight" placed on d4
    When I move piece from d4 to e6
    Then "White knight" should be placed on e6

  Scenario: Knight's illegal move
    Given there is a chessboard with "White knight" placed on d4
    When I move piece from d4 to f6
    Then the move is illegal
    And "White knight" should still be placed on d4