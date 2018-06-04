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
    And white bishop should be moved to e6

  Scenario: The pawn may not capture opponent's piece on the square immediately in front of it on the same file
    Given there is a chessboard
    And white pawn is placed on d4
    And black pawn is placed on d5
    When I move piece from d4 to d5
    Then the move is illegal
    And white pawn should not be moved from d4
    And black pawn should not be moved from d5

  Scenario: The pawn may move to a square occupied by an opponent’s piece diagonally in front of it on an adjacent file, capturing that piece
    Given there is a chessboard
    And white pawn is placed on d4
    And black pawn is placed on e5
    When I move piece from d4 to e5
    Then black pawn on e5 should be captured
    And white pawn should be moved to e5

  Scenario: A pawn occupying a square on the same rank as and on an adjacent file to an opponent’s pawn which has just advanced two squares in one move from its original square may capture this opponent’s pawn as though the latter had been moved only one square - such capture is called an 'en passant' capture
    Given there is a chessboard
    And following pieces are placed on it
      | piece      | location |
      | white pawn | d5       |
      | black pawn | e7       |
    And opponent moved piece from e7 to e5
    When I move piece from d5 to e6
    Then black pawn on e5 should be captured
    And white pawn should be moved to e6