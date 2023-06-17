<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Game;
use App\Models\player;

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

    public function getGames()
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

    public function getValidGames()
    {
        $currentDate = Carbon::today()->format('Y-m-d');

        $games = Game::whereDate('game_date', '>', $currentDate)->get();

        $validGames = [];

        foreach ($games as $game) {
            $playerCount = Player::where('game', $game->id)->count();
            $validGames[] = [
                'id' => $game->id,
                'game_title' => $game->game_title,
                'game_description' => $game->game_description,
                'game_date' => $game->game_date,
                'participants' => $playerCount,
            ];
        }

        return response()->json([
            "games" => $validGames,
        ], 200);
    }

    public function getPastGames()
    {
        $currentDate = Carbon::today()->format('Y-m-d');

        $games = Game::whereDate('game_date', '<', $currentDate)->get();

        $pastGames = [];

        foreach ($games as $game) {
            $playerCount = Player::where('game', $game->id)->count();
            $pastGames[] = [
                'id' => $game->id,
                'game_title' => $game->game_title,
                'game_description' => $game->game_description,
                'game_date' => $game->game_date,
                'participants' => $playerCount,
            ];
        }

        return response()->json([
            "games" => $pastGames,
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

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
            ], 422);
        }

        $players = Player::where('game', $req->game_id)->all();

        return response()->json([
            'players' => $players,
        ]);
    }
}
