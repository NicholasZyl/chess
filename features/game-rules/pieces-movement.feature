Feature: Pieces movement
  In order to play the chess
  As a player
  I need to move my pieces

  Scenario: King's legal move
    Given there is a chessboard with "White king" placed on D4
    When I move piece from D4 to E5
    Then "White king" should be placed on E5

  Scenario: King's illegal move
    Given there is a chessboard with "White king" placed on D4
    When I move piece from D4 to F8
    Then the move is illegal
    And "White king" should still be placed on D4