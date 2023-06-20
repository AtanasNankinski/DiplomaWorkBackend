<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Game;
use App\Models\Replica;
use App\Models\PastGame;
use App\Models\Score;
use App\Models\Player;

class GamesController extends Controller
{
    public function addGame(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'game_title' => 'required|string',
            'game_description' => 'required|string',
            'game_date' => 'required|string',
            'user_id' => 'required|int',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
            ], 422);
        }

        $user = User::where('id', $req->user_id)->first();
        if(!$user || $user->user_type != 1)
        {
            return response()->json([
                'message' => 'No such user or the current user doesn\'t have the required access rights.'
            ], 422);
        }

        $gameDate = Carbon::parse($req->game_date)->format('Y-m-d');

        $game = Game::create([
            'game_title' => $req->game_title,
            'game_description' => $req->game_description,
            'game_date' => $gameDate,
        ]);
        $game->save();

        return response()->json(['game' => $game], 201);
    }

    public function createPastGame(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'game_id' => 'required|integer',
            'team' => 'required|integer|in:2,3',
            'user_id' => 'required|int',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('id', $req->user_id)->first();
        if(!$user || $user->user_type != 1)
        {
            return response()->json([
                'message' => 'No such user or the current user doesn\'t have the required access rights.'
            ], 422);
        }
    
        $game = Game::find($req->game_id);
    
        if (!$game) {
            return response()->json([
                'message' => 'Game not found.',
            ], 404);
        }
    
        $pastGame = PastGame::create([
            'game_title' => $game->game_title,
            'game_description' => $game->game_description,
            'game_date' => $game->game_date,
            'victory_team' => $req->team,
        ]);
    
        $game->delete();

        $players = Player::where('game', $req->game_id)->get();
        foreach ($players as $player) {
            $score = Score::where('user', $player->user)->first();
            if ($score) {
                if ($player->team == $req->team) {
                    $score->victories += 1;
                    $score->last_game = $pastGame->id;
                } else {
                    $score->defeats += 1;
                    $score->last_game = $pastGame->id;
                }
                $score->save();
            }

            $player->game = $pastGame->id;
            $player->save();
        }
    
        return response()->json([
            'message' => 'Game moved to past games successfully.',
            'past_game' => $pastGame,
        ], 201);
    }

    public function getValidGames()
    {
        $games = Game::all();
        $finalGames = [];

        foreach ($games as $game) {
            $playerCount = Player::where('game', $game->id)->count();
            $finalGames[] = [
                'id' => $game->id,
                'game_title' => $game->game_title,
                'game_description' => $game->game_description,
                'game_date' => $game->game_date,
                'participants' => $playerCount,
            ];
        }

        return response()->json([
            "games" => $finalGames,
        ], 200);
    }

    public function getPastGames()
    {
        $games = PastGame::all();
        $finalGames = [];

        foreach ($games as $game) {
            $playerCount = Player::where('game', $game->id)->count();
            $finalGames[] = [
                'id' => $game->id,
                'game_title' => $game->game_title,
                'game_description' => $game->game_description,
                'game_date' => $game->game_date,
                'team' => $game->victory_team,
                'participants' => $playerCount,
            ];
        }

        return response()->json([
            "games" => $finalGames,
        ], 200);
    }

    public function createPlayer(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|int',
            'replica_id' => 'required|int',
            'game_id' => 'required|int',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
            ], 422);
        }

        $existingPlayer = Player::where('user', $req->user_id)
                                ->where('game', $req->game_id)
                                ->first();

        if ($existingPlayer) {
            return response()->json([
                'message' => 'Player already exists for the given user and game.'
            ], 422);
        }

        $player = Player::create([
            'user' => $req->user_id,
            'replica' => $req->replica_id,
            'game' => $req->game_id,
            'team' => 1,
        ]);
        $player->save();

        return response()->json([
            'player' => $player
        ], 201);
    }

    public function getPlayers(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'game_id' => 'required|int',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
            ], 422);
        }
    
        $players = Player::where('game', $req->game_id)->get();
    
        $formattedPlayers = $players->map(function ($player) {
            $user = User::find($player->user);
            $replica = Replica::find($player->replica);
    
            return [
                'id' => $player->id,
                'user' => $user->name,
                'replica' => $replica,
                'team' => $player->team,
            ];
        });
    
        return response()->json([
            'players' => $formattedPlayers,
        ], 200);
    }

    public function updatePlayerTeam(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'player_id' => 'required|int',
            'team' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validator fails.',
            ], 422);
        }

        $player = Player::find($req->player_id);

        if (!$player) {
            return response()->json([
                'message' => 'Player not found.',
            ], 404);
        }

        $player->team = $req->team;
        $player->save();

        $players = Player::where('game', $player->game)->get();
    
        $formattedPlayers = $players->map(function ($player) {
            $user = User::find($player->user);
            $replica = Replica::find($player->replica);
    
            return [
                'id' => $player->id,
                'user' => $user->name,
                'replica' => $replica,
                'team' => $player->team,
            ];
        });
    
        return response()->json([
            'players' => $formattedPlayers,
        ], 200);
    }
}
