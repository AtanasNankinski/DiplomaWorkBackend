<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Replica;

class ReplicaController extends Controller
{
    public function addReplica(Request $req)
    {
        $user_id = $req->user_id;

        $validator = Validator::make($req->all(), [
            'replica_name' => [
                'required',
                Rule::unique('replicas')->where(function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id);
                })
            ],
            'replica_type' => 'required|string',
            'replica_power' => 'required|numeric',
            'user_id' => 'required|int',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
            ], 422);
        }

        $replica = Replica::create([
            'replica_name' => $req->replica_name,
            'replica_type' => $req->replica_type,
            'replica_power' => $req->replica_power,
            'user_id' => $req->user_id
        ]);

        $replica->save();

        return response()->json([
            'replica_name' => $replica->replica_name,
            'replica_type' => $replica->replica_type,
            'replica_power' => $replica->replica_power
        ]);
    }

    public function getReplicas(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|int',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
            ], 422);
        }

        $replicas = Replica::where('user_id', $req->user_id)->get();

        if(!$replicas)
        {
            return response()->json([
                'message'=>"There are no replicas for that user."
            ], 404);
        }

        return response()->json([
            'replicas' => $replicas
        ], 200);
    }

    public function editReplica(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'replica_name' => 'required|string',
            'replica_type' => 'required|string',
            'replica_power' => 'required|numeric',
            'user_id' => 'required|int',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
            ], 422);
        }

        $replica = Replica::where('user_id', $req->user_id)->where('replica_name', $req->replica_name)->first();

        if(!$replica)
        {
            return response()->json([
                'message'=>"There are no replicas for that user."
            ], 404);
        }

        $replica->replica_type = $req->replica_type;
        $replica->replica_power = $req->replica_power;
        $replica->save();

        return response()->json([
            'replica_name' => $replica->replica_name,
            'replica_type' => $replica->replica_type,
            'replica_power' => $replica->replica_power
        ], 200);
    }

    public function deleteReplica(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'replica_name' => 'required|string',
            'user_id' => 'required|int',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'Validator fails.'
            ], 422);
        }

        $replica = Replica::where('user_id', $req->user_id)->where('replica_name', $req->replica_name)->delete();

        return response()->json([
            'message' => "Replica deleted successfully.",
        ], 200);
    }
}