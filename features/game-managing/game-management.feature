Feature: Managing games
  In order to play the chess
  As a player
  I need to be able to manage games

  @application @ui @web @console
  Scenario: Setting up a new game
    When I setup the game
    Then game should be set with initial positions of the pieces on the chessboard

  @application @ui @web @console
  Scenario: Failing to find a game
    When I try to find a non existing game
    Then I should not find the game

  @application @ui @web @console
  Scenario: Playing in non existing game
    When I try to move piece from c2 to c3
    Then I should not find the game