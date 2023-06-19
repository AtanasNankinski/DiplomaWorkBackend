<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastGame extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'game_title',
        'game_description',
        'game_date',
        'victory_team',
    ];
}
