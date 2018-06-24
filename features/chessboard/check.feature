Feature: The king's check
  In order to win the game
  As a player
  I need to check my opponent's king

  Scenario: The king is checked after move
    Given there is a chessboard with placed pieces
      | piece       | location |
      | black king  | h8       |
      | white rook  | d3       |
    When I move piece from d3 to d8
    Then black is in check