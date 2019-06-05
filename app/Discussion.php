<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = [
        'send_id', 'receive_id', 'room_id', 'message'
    ];
}
