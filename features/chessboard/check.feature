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

  Scenario: No piece can be moved that will expose the king of the same colour to check
    Given there is a chessboard with placed pieces
      | piece       | location |
      | black king  | e8       |
      | black rook  | f8       |
      | white rook  | d8       |
    When I move piece from f8 to f1
    Then the move is illegal
    And black rook should not be moved from f8