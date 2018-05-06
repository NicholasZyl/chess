Feature: The capture of opponent's piece
  In order to win the game
  As a player
  I need to capture opponents' pieces

  Scenario: If a piece moves to a square occupied by an opponent’s piece the latter is captured and removed from the chessboard as part of the same move
    Given there is a chessboard
    And following pieces are placed on it
      | piece        | location |
      | white bishop | d5       |
      | black pawn   | e6       |
    When I move piece from d5 to e6
    Then black pawn on e6 should be captured
    And white bishop should be placed on e6

  Scenario: The pawn may not capture opponent's piece on the square immediately in front of it on the same file
    Given there is a chessboard
    And white pawn is placed on d4
    And black pawn is placed on d5
    When I move piece from d4 to d5
    Then the move is illegal
    And white pawn should still be placed on d4
    And black pawn should still be placed on d5