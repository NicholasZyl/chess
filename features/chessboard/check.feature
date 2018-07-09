Feature: The king's check
  In order to win the game
  As a player
  I need to check my opponent's king

  Scenario: The king is checked after move when king is attacked
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

  Scenario: No piece can be moved that will leave king in check
    Given there is a chessboard with placed pieces
      | piece       | location |
      | black king  | e8       |
      | black rook  | f8       |
      | white rook  | e1       |
    When I move piece from f8 to f1
    Then the move is illegal
    And black rook should not be moved from f8

  Scenario: If opponent's king is in check and opponent has no legal move then it's checkmated
    Given there is a chessboard with placed pieces
      | piece       | location |
      | white king  | e1       |
      | black rook  | a8       |
      | black rook  | g2       |
    When I move piece from a8 to a1
    Then white is checkmated