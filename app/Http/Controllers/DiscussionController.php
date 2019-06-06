<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Online;
use App\Discussion;
use App\Room;

// use App\sock;

session_start();

class DiscussionController extends Controller
{
    public function init(Request $request)
    {
        // 初始化信息
        /**
         * 1.个人信息
         * 1.1name
         * 1.2style
         * 1.3uid
         * 1.4rid
         * 2.房间信息
         * 2.1人数
         * 2.2房主id
         * 2.3音乐插件
         * 
         */

        // $room = DB::table('rooms')
        //     ->where('id', $_SESSION['rid'])
        //     ->select('size', 'owner_id')

        return response()->json([
            'data' => $_SESSION['rid']
        ]);
    }
}
