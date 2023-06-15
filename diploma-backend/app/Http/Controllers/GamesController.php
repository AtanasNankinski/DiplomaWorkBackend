<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Game;

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
}
