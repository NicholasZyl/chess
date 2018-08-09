Feature: Making moves
  In order to play the chess
  As players
  We need to make our moves

  @application
  Scenario: White makes the first move
    Given the game is set up
    When I move piece from b1 to a3
    Then white knight should be moved to a3

  @application
  Scenario: Black cannot make the first move
    Given the game is set up
    When opponent tries to move piece from d7 to d5
    Then the move is illegal
    And black pawn should not be moved from d7

  Scenario: Players make moves alternately
    Given the game is set up
    And I moved piece from b1 to a3
    And opponent moved piece from g7 to g5
    When opponent tries to move piece from c7 to c6
    Then the move is illegal
    And black pawn should not be moved from c7