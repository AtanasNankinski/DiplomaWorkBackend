<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestingController extends Controller
{
    public function testApiConnection() 
    {
        return response()->json(['message' => "Connection successful!"]);
    }
}
