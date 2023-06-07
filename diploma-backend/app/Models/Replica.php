<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Replica extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'replica_name',
        'replica_type',
        'replica_power',
        'user_id',
    ];
}
