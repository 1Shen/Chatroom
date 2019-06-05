<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Online;
use App\Discussion;
use App\Room;

session_start();

class DiscussionController extends Controller
{
    public function init(Request $request)
    {
        // 初始化信息
        
    }
}
