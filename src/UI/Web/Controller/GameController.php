<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Web\Controller;

use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\GameId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController
{
    /**
     * API action to get a state of the game with passed identifier.
     *
     * @param GameId $identifier
     * @param GameService $gameService
     *
     * @return Response
     */
    public function getState(GameId $identifier, GameService $gameService): Response
    {
        $game = $gameService->find($identifier);

        return JsonResponse::create($game);
    }

    /**
     * API action to play a move in the game with passed identifier.
     *
     * @param GameId $identifier
     * @param Request $request
     * @param GameService $gameService
     *
     * @return Response
     */
    public function playMove(GameId $identifier, Request $request, GameService $gameService): Response
    {
        if (!$request->request->has('from') || !$request->request->has('to')) {
            return JsonResponse::create(
                [
                    'message' => 'Request to move a piece requires "from" and "to" coordinates to be passed.',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $gameService->movePieceInGame($identifier, $request->get('from'), $request->get('to'));

            return Response::create(null, Response::HTTP_NO_CONTENT);
        } catch (\RuntimeException $exception) {
            return JsonResponse::create(
                [
                    'message' => $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}