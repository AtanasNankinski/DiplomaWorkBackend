<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Carbon\Carbon;
use App\Models\Game;

class GamesController extends Controller
{
    public function addGame(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'game_title' => 'required|string',
            'game_description' => 'required|string',
            'game_date' => 'required|string',
        ]);

        //return response()->json(['title' => $req->game_title, 'desc' => $req->game_description, 'date' => $req->game_date]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
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
