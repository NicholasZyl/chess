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