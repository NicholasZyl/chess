Feature: The moves of the pieces
  In order to play the chess
  As a player
  I need to move my pieces

  Scenario: The bishop may move to any square along a diagonal on which it stands
    Given there is a chessboard with white bishop placed on d4
    When I move piece from d4 to g7
    Then white bishop should be placed on g7

  Scenario: The bishop may not move to a square along the file or the rank
    Given there is a chessboard with black bishop placed on d4
    When I move piece from d4 to d5
    Then the move is illegal
    And black bishop should still be placed on d4

  Scenario: The rook may move to any square along the file or the rank on which it stands
    Given there is a chessboard with white rook placed on d4
    When I move piece from d4 to d8
    Then white rook should be placed on d8

  Scenario: The rook may not move to a square along a diagonal
    Given there is a chessboard with white rook placed on d4
    When I move piece from d4 to e5
    Then the move is illegal
    And white rook should still be placed on d4

  Scenario: The queen may move to any square along the file, the rank or a diagonal on which it stands
    Given there is a chessboard with white queen placed on d4
    When I move piece from d4 to g1
    Then white queen should be placed on g1

  Scenario: The queen may not move in direction other than along the file, the rank or a diagonal
    Given there is a chessboard with black queen placed on d4
    When I move piece from d4 to g2
    Then the move is illegal
    And black queen should still be placed on d4

  Scenario: When making moves, the bishop, rook or queen may not move over any intervening pieces
    Given there is a chessboard
    And black pawn is placed on f7
    And white bishop is placed on d5
    When I move piece from d5 to g8
    Then the move is illegal
    And white bishop should still be placed on d5
    And black pawn should still be placed on f7

  Scenario: The knight may move to one of the squares nearest to that on which it stands but not on the same rank, file or diagonal
    Given there is a chessboard with white knight placed on d4
    When I move piece from d4 to e6
    Then white knight should be placed on e6

  Scenario: The knight may not move to squares further from the square on which it stands
    Given there is a chessboard with white knight placed on d4
    When I move piece from d4 to f6
    Then the move is illegal
    And white knight should still be placed on d4

  Scenario: The knight may move over intervening pieces
    Given there is a chessboard
    And following pieces are placed on it
      | piece        | location |
      | black knight | g8       |
      | black rook   | h8       |
      | black king   | f8       |
      | black pawn   | f7       |
      | black pawn   | g7       |
      | black pawn   | h7       |
    When I move piece from g8 to f6
    Then black knight should be placed on f6

  Scenario: The pawn may move forward to the square immediately in front of it on the same file, provided that this square is unoccupied
    Given there is a chessboard with white pawn placed on d4
    When I move piece from d4 to d5
    Then white pawn should be placed on d5

  Scenario: On its first move the pawn may advance two squares along the same file, provided that both squares are unoccupied
    Given there is a chessboard with white pawn placed on b2
    When I move piece from b2 to b4
    Then white pawn should be placed on b4

  Scenario: The pawn may not advance two squares along the same file, if any square is occupied
    Given there is a chessboard
    And following pieces are placed on it
      | piece        | location |
      | white pawn   | c2       |
      | white knight | c3       |
    And I tried to move piece from c2 to c4
    But the move was illegal
    When I moved piece from c3 to e4
    And I move piece from c2 to c4
    Then white pawn should be placed on c4

  Scenario: The pawn may not move forward if the square is occupied
    Given there is a chessboard
    And white pawn is placed on d4
    And black pawn is placed on d5
    When I move piece from d4 to d5
    Then the move is illegal
    And white pawn should still be placed on d4
    And black pawn should still be placed on d5

  Scenario: The pawn may not advance more than one square forward if not on first move
    Given there is a chessboard with white pawn placed on d2
    When I move piece from d2 to d4
    When I move piece from d4 to d6
    Then the move is illegal
    And white pawn should still be placed on d4

  Scenario: The pawn may advance two squares along the same file if previous move was illegal and not made
    Given there is a chessboard with white pawn placed on b2
    And I tried to move piece from b2 to b1
    But the move was illegal
    When I move piece from b2 to b4
    Then white pawn should be placed on b4

  Scenario: The king may move to an adjoining square
    Given there is a chessboard with white king placed on d4
    When I move piece from d4 to e5
    Then white king should be placed on e5

  Scenario: The king may not move by more than one square
    Given there is a chessboard with white king placed on d4
    When I move piece from d4 to f8
    Then the move is illegal
    And white king should still be placed on d4

  Scenario: It is not permitted to move a piece to a square occupied by a piece of the same colour
    Given there is a chessboard with white pawn placed on d5
    And white bishop is placed on b3
    When I move piece from b3 to d5
    Then the move is illegal
    And white bishop should still be placed on b3