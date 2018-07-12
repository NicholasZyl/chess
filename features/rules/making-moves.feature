Feature: Making moves
  In order to play the chess
  As players
  We need to make our moves

  Scenario: White makes the first move
    Given the game is set up
    When I move piece from b1 to a3
    Then white knight should be moved to a3

  Scenario: Black cannot make the first move
    Given the game is set up
    When I try to move piece from d7 to d5
    Then the move is illegal
    And black pawn should not be moved from d7