Feature: The moves of the pieces
  In order to play the chess
  As a player
  I need to move my pieces

  Scenario: The bishop may move to any square along a diagonal on which it stands
    Given there is a chessboard with white bishop placed on d4
    When I move piece from d4 to g7
    Then white bishop should be moved to g7

  Scenario: The bishop may not move to a square along the file or the rank
    Given there is a chessboard with black bishop placed on d4
    When I try to move piece from d4 to d5
    Then the move is illegal
    And black bishop should not be moved from d4

  Scenario: The rook may move to any square along the file or the rank on which it stands
    Given there is a chessboard with white rook placed on d4
    When I move piece from d4 to d8
    Then white rook should be moved to d8

  Scenario: The rook may not move to a square along a diagonal
    Given there is a chessboard with white rook placed on d4
    When I try to move piece from d4 to e5
    Then the move is illegal
    And white rook should not be moved from d4

  Scenario: The queen may move to any square along the file, the rank or a diagonal on which it stands
    Given there is a chessboard with white queen placed on d4
    When I move piece from d4 to g1
    Then white queen should be moved to g1

  Scenario: The queen may not move in direction other than along the file, the rank or a diagonal
    Given there is a chessboard with black queen placed on d4
    When I try to move piece from d4 to g2
    Then the move is illegal
    And black queen should not be moved from d4

  Scenario: When making moves, the bishop, rook or queen may not move over any intervening pieces
    Given there is a chessboard with placed pieces
      | piece        | location |
      | black pawn   | f7       |
      | white bishop | d5       |
    When I try to move piece from d5 to g8
    Then the move is illegal
    And white bishop should not be moved from d5

  Scenario: The knight may move to one of the squares nearest to that on which it stands but not on the same rank, file or diagonal
    Given there is a chessboard with white knight placed on d4
    When I move piece from d4 to e6
    Then white knight should be moved to e6

  Scenario: The knight may not move to squares further from the square on which it stands
    Given there is a chessboard with white knight placed on d4
    When I try to move piece from d4 to f6
    Then the move is illegal
    And white knight should not be moved from d4

  Scenario: The knight may move over intervening pieces
    Given there is a chessboard with placed pieces
      | piece        | location |
      | black knight | g8       |
      | black rook   | h8       |
      | black king   | f8       |
      | black pawn   | f7       |
      | black pawn   | g7       |
      | black pawn   | h7       |
    When I move piece from g8 to f6
    Then black knight should be moved to f6

  Scenario: The pawn may move forward to the square immediately in front of it on the same file, provided that this square is unoccupied
    Given there is a chessboard with white pawn placed on d4
    When I move piece from d4 to d5
    Then white pawn should be moved to d5

  Scenario: On its first move the pawn may advance two squares along the same file, provided that both squares are unoccupied
    Given there is a chessboard with white pawn placed on b2
    When I move piece from b2 to b4
    Then white pawn should be moved to b4

  Scenario: The pawn may not advance two squares along the same file, if any square is occupied
    Given there is a chessboard with placed pieces
      | piece        | location |
      | white pawn   | c2       |
      | white knight | c3       |
    And I tried to move piece from c2 to c4
    But the move was illegal
    When I moved piece from c3 to e4
    And I move piece from c2 to c4
    Then white pawn should be moved to c4

  Scenario: The pawn may not move forward if the square is occupied
    Given there is a chessboard with placed pieces
      | piece        | location |
      | white pawn   | d4       |
      | white knight | d5       |
    When I try to move piece from d4 to d5
    Then the move is illegal
    And white pawn should not be moved from d4
    And white knight should not be moved from d5

  Scenario: The pawn may not advance more than one square forward if not on first move
    Given there is a chessboard with white pawn placed on d2
    And I moved piece from d2 to d4
    When I try to move piece from d4 to d6
    Then the move is illegal
    And white pawn should not be moved from d2

  Scenario: The pawn may advance two squares along the same file if previous move was illegal and not made
    Given there is a chessboard with white pawn placed on b2
    And I tried to move piece from b2 to b1
    But the move was illegal
    When I move piece from b2 to b4
    Then white pawn should be moved to b4

  Scenario: The king may move to an adjoining square
    Given there is a chessboard with white king placed on d4
    When I move piece from d4 to e5
    Then white king should be moved to e5

  Scenario: The king may not move by more than one square
    Given there is a chessboard with white king placed on d4
    When I try to move piece from d4 to f8
    Then the move is illegal
    And white king should not be moved from d4

  Scenario: The king may move by 'castling'
    Given there is a chessboard with placed pieces
      | piece      | location |
      | white king | e1       |
      | white rook | a1       |
    When I move piece from e1 to c1
    Then white king should be moved to c1
    And white rook should be moved to d1

  Scenario: Castling is prevented temporarily if the square on which the king stands, or the square which it must cross, or the square which it is to occupy, is attacked by one or more of the opponent's pieces
    Given there is a chessboard with placed pieces
      | piece       | location |
      | white king  | e1       |
      | white rook  | a1       |
      | black queen | d8       |
    When I try to move piece from e1 to c1
    Then the move is illegal
    And white king should not be moved from e1

  Scenario: Castling is prevented temporarily if there is any piece between the king and the rook with which castling is to be effected
    Given there is a chessboard with placed pieces
      | piece        | location |
      | white king   | e1       |
      | white rook   | a1       |
      | white bishop | b1       |
    When I try to move piece from e1 to c1
    Then the move is illegal
    And white king should not be moved from e1

  Scenario: The right to castle has been lost if king has already moved
    Given there is a chessboard with placed pieces
      | piece        | location |
      | black king   | e8       |
      | black rook   | h8       |
    And I moved piece from e8 to e7
    And I moved piece from e7 to e8
    When I try to move piece from e8 to g8
    Then the move is illegal
    And black king should not be moved from e8

  Scenario: The right to castle has been lost if rook has already moved
    Given there is a chessboard with placed pieces
      | piece        | location |
      | black king   | e8       |
      | black rook   | a8       |
    And I moved piece from a8 to b8
    And I moved piece from b8 to a8
    When I try to move piece from e8 to c8
    Then the move is illegal
    And black king should not be moved from e8

  Scenario: The right to castle has been lost if rook was captured
    Given there is a chessboard with placed pieces
      | piece        | location |
      | white king   | e1       |
      | white rook   | a1       |
      | black queen  | a4       |
    And opponent moved piece from a4 to a1
    And opponent moved piece from a1 to a2
    When I try to move piece from e1 to c1
    Then the move is illegal
    And white king should not be moved from e1

  Scenario: It is not permitted to move a piece to a square occupied by a piece of the same colour
    Given there is a chessboard with placed pieces
      | piece        | location |
      | white pawn   | d5       |
      | white bishop | b3       |
    When I try to move piece from b3 to d5
    Then the move is illegal
    And white bishop should not be moved from b3

  Scenario: Pawn's promotion
    Given there is a chessboard with white pawn placed on b7
    And I moved piece from b7 to b8
    When I exchange piece on b8 to white queen
    Then white pawn on b8 should be exchanged with white queen