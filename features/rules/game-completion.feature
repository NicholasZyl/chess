Feature: The completion of game
  In order to win the game
  As a player
  I need to check my opponent's king

  @application @ui @web @console
  Scenario: The king is checked after move when king is attacked
    Given there is a chessboard with placed pieces
      | piece       | location |
      | Black king  | e8       |
      | White rook  | d3       |
    When I move piece from d3 to d8
    Then Black is in check

  Scenario: No piece can be moved that will expose the king of the same colour to check
    Given there is a chessboard with placed pieces
      | piece       | location |
      | White king  | e1       |
      | White rook  | f1       |
      | Black rook  | d1       |
    When I move piece from f1 to f8
    Then the move is illegal
    And White rook should not be moved from f1

  Scenario: No piece can be moved that will leave king in check
    Given there is a chessboard with placed pieces
      | piece       | location |
      | Black king  | e8       |
      | Black rook  | f8       |
      | White rook  | e1       |
    When I move piece from f8 to f1
    Then the move is illegal
    And Black rook should not be moved from f8

  @application @ui @web @console
  Scenario: If opponent's king is in check and opponent has no legal move then it's checkmated
    Given there is a chessboard with placed pieces
      | piece       | location |
      | White king  | f1       |
      | Black rook  | a8       |
      | Black rook  | g2       |
    And I moved piece from f1 to e1
    When opponent move piece from a8 to a1
    Then White is checkmated
    And Black won the game

  Scenario: If player has no legal move and his king is not in checkmate then the game ends with stalemate
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White king   | e1       |
      | Black bishop | g4       |
      | Black rook   | a8       |
      | Black rook   | d7       |
    And it is Black turn
    When opponent move piece from a8 to f8
    Then it is stalemate
    And the game ends with drawn