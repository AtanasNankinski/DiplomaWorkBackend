<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PastGame;
use App\Models\Score;
use App\Models\Player;

class ScoreController extends Controller
{
    public function getScore(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validator fails.',
            ], 422);
        }

        $score = Score::where('user', $req->user_id)->first();

        if (!$score) {
            return response()->json([
                'message' => 'Player not found.',
            ], 404);
        }

        $lastGame = null;
        $player = null;
        if($score->last_game != null)
        {
            $lastGame = PastGame::where('id', $score->last_game)->first();
            $playerCount = Player::where('game', $lastGame->id)->count();
            $finalGame = [
                'id' => $lastGame->id,
                'game_title' => $lastGame->game_title,
                'game_description' => $lastGame->game_description,
                'game_date' => $lastGame->game_date,
                'team' => $lastGame->victory_team,
                'participants' => $playerCount,
            ];
            $player = Player::where('game', $lastGame->id)->first();
        }

        return response()->json([
            'id'=>$score->id,
            'victories'=>$score->victories,
            'defeats'=>$score->defeats,
            'last_game'=>$finalGame,
            'last_team'=>$player->team,
        ], 200);
    }
}
