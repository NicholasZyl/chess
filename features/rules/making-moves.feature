Feature: Making moves
  In order to play the chess
  As players
  We need to make our moves

  Scenario: White makes the first move
    Given the game is set up
    When I move piece from b1 to a3
    Then white knight should be moved from b1