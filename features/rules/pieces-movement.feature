Feature: The moves of the pieces
  In order to play the chess
  As a player
  I need to move my pieces

  @ui @web
  Scenario: The bishop may move to any square along a diagonal on which it stands
    Given there is a chessboard with White bishop placed on d4
    When I move piece from d4 to g7
    Then White bishop should be moved to g7

  @ui @web
  Scenario: The bishop may not move to a square along the file or the rank
    Given there is a chessboard with Black bishop placed on d4
    When I try to move piece from d4 to d5
    Then the move is illegal
    And Black bishop should not be moved from d4

  Scenario: The rook may move to any square along the file or the rank on which it stands
    Given there is a chessboard with White rook placed on d4
    When I move piece from d4 to d8
    Then White rook should be moved to d8

  Scenario: The rook may not move to a square along a diagonal
    Given there is a chessboard with White rook placed on d4
    When I try to move piece from d4 to e5
    Then the move is illegal
    And White rook should not be moved from d4

  @ui @console
  Scenario: The queen may move to any square along the file, the rank or a diagonal on which it stands
    Given there is a chessboard with White queen placed on d4
    When I move piece from d4 to g1
    Then White queen should be moved to g1

  @ui @console
  Scenario: The queen may not move in direction other than along the file, the rank or a diagonal
    Given there is a chessboard with Black queen placed on d4
    When I try to move piece from d4 to g2
    Then the move is illegal
    And Black queen should not be moved from d4

  @application
  Scenario: When making moves, the bishop, rook or queen may not move over any intervening pieces
    Given there is a chessboard with placed pieces
      | piece        | location |
      | Black pawn   | f7       |
      | White bishop | d5       |
    When I try to move piece from d5 to g8
    Then the move is illegal
    And White bishop should not be moved from d5

  @application
  Scenario: The knight may move to one of the squares nearest to that on which it stands but not on the same rank, file or diagonal
    Given there is a chessboard with White knight placed on d4
    When I move piece from d4 to e6
    Then White knight should be moved to e6

  Scenario: The knight may not move to squares further from the square on which it stands
    Given there is a chessboard with White knight placed on d4
    When I try to move piece from d4 to f6
    Then the move is illegal
    And White knight should not be moved from d4

  Scenario: The knight may move over intervening pieces
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White knight | g8       |
      | White rook   | h8       |
      | White king   | f8       |
      | White pawn   | f7       |
      | White pawn   | g7       |
      | White pawn   | h7       |
    When I move piece from g8 to f6
    Then White knight should be moved to f6

  Scenario: The pawn may move forward to the square immediately in front of it on the same file, provided that this square is unoccupied
    Given there is a chessboard with White pawn placed on d4
    When I move piece from d4 to d5
    Then White pawn should be moved to d5

  @ui @console
  Scenario: On its first move the pawn may advance two squares along the same file, provided that both squares are unoccupied
    Given there is a chessboard with White pawn placed on b2
    When I move piece from b2 to b4
    Then White pawn should be moved to b4

  @ui @console
  Scenario: The pawn may not advance two squares along the same file, if any square is occupied
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White pawn   | c2       |
      | White knight | c3       |
      | Black knight | g5       |
    And I tried to move piece from c2 to c4
    But the move was illegal
    And I moved piece from c3 to e4
    And opponent moved piece from g5 to h7
    When I move piece from c2 to c4
    Then White pawn should be moved to c4

  Scenario: The pawn may not move forward if the square is occupied
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White pawn   | d4       |
      | White knight | d5       |
    When I try to move piece from d4 to d5
    Then the move is illegal
    And White pawn should not be moved from d4
    And White knight should not be moved from d5

  Scenario: The pawn may not advance more than one square forward if not on first move
    Given there is a chessboard with White pawn placed on d2
    And I moved piece from d2 to d4
    When I try to move piece from d4 to d6
    Then the move is illegal
    And White pawn should not be moved from d2

  Scenario: The pawn may advance two squares along the same file if previous move was illegal and not made
    Given there is a chessboard with White pawn placed on b2
    And I tried to move piece from b2 to b1
    But the move was illegal
    When I move piece from b2 to b4
    Then White pawn should be moved to b4

  Scenario: The king may move to an adjoining square
    Given there is a chessboard with White king placed on d4
    When I move piece from d4 to e5
    Then White king should be moved to e5

  Scenario: The king may not move by more than one square
    Given there is a chessboard with White king placed on d4
    When I try to move piece from d4 to f8
    Then the move is illegal
    And White king should not be moved from d4

  @ui @web
  Scenario: The king may move by 'castling'
    Given there is a chessboard with placed pieces
      | piece      | location |
      | White king | e1       |
      | White rook | a1       |
    When I move piece from e1 to c1
    Then White king should be moved to c1
    And White rook should be moved to d1

  @ui @web @console
  Scenario: Castling is prevented temporarily if the square on which the king stands, or the square which it must cross, or the square which it is to occupy, is attacked by one or more of the opponent's pieces
    Given there is a chessboard with placed pieces
      | piece       | location |
      | White king  | e1       |
      | White rook  | a1       |
      | Black queen | d8       |
    When I try to move piece from e1 to c1
    Then the move is illegal
    And White king should not be moved from e1

  @ui @console
  Scenario: Castling is prevented temporarily if there is any piece between the king and the rook with which castling is to be effected
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White king   | e1       |
      | White rook   | a1       |
      | White bishop | b1       |
    When I try to move piece from e1 to c1
    Then the move is illegal
    And White king should not be moved from e1

  Scenario: The right to castle has been lost if king has already moved
    Given there is a chessboard with placed pieces
      | piece        | location |
      | Black king   | e7       |
      | Black rook   | h8       |
      | White bishop | b1       |
    And it is Black turn
    And opponent moved piece from e7 to e8
    And I moved piece from b1 to c2
    When opponent tries to move piece from e8 to g8
    Then the move is illegal
    And Black king should not be moved from e8

  Scenario: The right to castle has been lost if rook has already moved
    Given there is a chessboard with placed pieces
      | piece        | location |
      | Black king   | e8       |
      | Black rook   | b8       |
      | White king   | e1       |
    And it is Black turn
    And opponent moved piece from b8 to a8
    And I moved piece from e1 to e2
    When opponent tries to move piece from e8 to c8
    Then the move is illegal
    And Black king should not be moved from e8

  Scenario: The right to castle has been lost if rook was captured
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White king   | e1       |
      | White rook   | a5       |
      | White rook   | a1       |
      | Black queen  | a4       |
    And it is Black turn
    And opponent moved piece from a4 to a1
    And I moved piece from a5 to a1
    When I try to move piece from e1 to c1
    Then the move is illegal
    And White king should not be moved from e1

  Scenario: It is not permitted to move a piece to a square occupied by a piece of the same colour
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White pawn   | d5       |
      | White bishop | b3       |
    When I try to move piece from b3 to d5
    Then the move is illegal
    And White bishop should not be moved from b3

  @application @ui @web @console
  Scenario: Pawn's promotion
    Given there is a chessboard with White pawn placed on b7
    And I moved piece from b7 to b8
    When I exchange piece on b8 for White queen
    Then White pawn on b8 should be exchanged for White queen

  @ui @web @console
  Scenario: Pawn's promotion can happen only on the promotion square
    Given there is a chessboard with White pawn placed on b6
    And I moved piece from b6 to b7
    When I try to exchange piece on b7 for White queen
    Then the exchange is illegal
    And White pawn on b7 should not be exchanged for White queen

  @ui @web @console
  Scenario: Pawn's promotion has to be done as part of the move
    Given there is a chessboard with placed pieces
      | piece        | location |
      | White pawn   | b7       |
      | Black pawn   | d7       |
    And I moved piece from b7 to b8
    When opponent tries to move piece from d7 to d6
    Then the move is illegal
    And Black pawn should not be moved from d7