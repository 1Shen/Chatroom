<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Room;
use App\User;
use App\Online;

session_start();

class RoomController extends Controller
{
    public function list()
    {
        /**
         * 1.返回个人信息
         * 2.返回在线房间及人数
         * 3.打印房间列表（包括房间名，房主名，人数上限，*房间描述，*在线人列表）
         */

        // 信息
        $name = $_SESSION['name'];
        $style = $_SESSION['style'];
        $uid = $_SESSION['uid'];
        $rid = $_SESSION['rid'];

        // 在线
        $rCount = DB::table('onlines')
            ->distinct('rid')
            ->count('rid')
            - 1;
        $uCount = DB::table('onlines')
            ->count('uid');

        // 列表
        $lists = DB::table('onlines')
            ->join('rooms', 'rooms.id', '=', 'onlines.rid')
            ->join('users', 'users.id', '=', 'onlines.uid')
            ->where('onlines.rid', '<>', 1)
            ->select('rooms.name as roomname', 'rooms.description', 'rooms.owner_id', 'rooms.size', 'rooms.id as roomid', 'rooms.open')
            ->distinct('rooms.id')
            ->get();

        foreach ($lists as $list) {
            $owner = DB::table('users')
                ->where('id', $list->owner_id)
                ->select('name', 'style')
                ->get();
            $list->owner = $owner;
            $count = DB::table('onlines')
                ->where('rid', $list->roomid)
                ->count('uid');
            $list->count = $count;
            $members = DB::table('onlines')
                ->join('users', 'users.id', '=', 'onlines.uid')
                ->where('onlines.rid', $list->roomid)
                ->select('users.name', 'users.style')
                ->get();
            $list->members = $members;
        }


        return response()->json([
            'name' => $name,
            'style' => $style,
            'uid' => $uid,
            'rid' => $rid,
            'rCount' => $rCount,
            'uCount' => $uCount,
            'lists' => $lists
        ]);
    }

    public function enter(Request $request)
    {
        // 进入房间

        $errCode = 100;
        $errMsg = "";

        // 检测人数
        $count = DB::table('onlines')
            ->where('onlines.rid', $request->input('rid'))
            ->count('uid');
        $size = DB::table('rooms')
            ->where('rooms.id', $request->input('rid'))
            ->select('rooms.size')
            ->get()[0];
        $max = "";
        foreach ($size as $index => $value) {
            $max .= $value;
        }
        $max = (int)$max;
        if ($count >= $max) {
            $errMsg = "room full";
        } else {
            // 加入房间
            $online = DB::table('onlines')
                ->where('onlines.uid', $_SESSION['uid'])
                ->update(['rid' => $request->input('rid')]);
            if ($online) {
                $errCode = 200;
                $errMsg = "enter success";
                $_SESSION['rid'] = $request->input('rid');
            } else {
                $errMsg = "update fail";
            }
        }

        return response()->json([
            'errCode' => $errCode,
            'errMsg' => $errMsg
        ]);
    }

    public function create(Request $request)
    {
        // 新建房间

        $errCode = 100;
        $errMsg = "";

        // 检测重名
        $check = DB::table('onlines')
            ->join('rooms', 'onlines.rid', '=', 'rooms.id')
            ->where('rooms.name', $request->input('name'))
            ->count('rooms.name');
        if ($check != 0) {
            $errMsg = "room exist";
        } else {
            $room = Room::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'size' => $request->input('size'),
                'owner_id' => $_SESSION['uid'],
                'open' => $request->input('open')
            ]);

            if ($room) {
                $errCode = 200;
                $errMsg = "create success";
                $_SESSION['rid'] = $room['id'];

                $online = DB::table('onlines')
                    ->where('onlines.uid', $_SESSION['uid'])
                    ->update(['rid' => $_SESSION['rid']]);
            }
        }

        return response()->json([
            'errCode' => $errCode,
            'errMsg' => $errMsg
        ]);
    }

    public function exit()
    {
        // 离开房间

        $errCode = 100;
        $errMsg = "";

        $room = DB::table('rooms')
            ->where('rooms.id', $_SESSION['rid']);
        $owner = $room->value('rooms.owner_id');

        if ($owner == $_SESSION['uid']) {
            $candidate = DB::table('onlines')
                ->where('onlines.rid', $_SESSION['rid'])
                ->where('onlines.uid', '<>', $owner)
                ->select('onlines.uid')
                ->first();

            if (!$candidate) {
                $room->update(['owner_id' => '1']);
            } else {
                $num = "";
                foreach ($candidate as $index => $value) {
                    $num .= $value;
                }
                $num = (int)$num;
                $room->update(['owner_id' => $num]);
            }
        }

        $online = DB::table('onlines')
            ->where('onlines.uid', $_SESSION['uid'])
            ->update(['rid' => '1']);
        if ($online) {
            $errCode = 200;
            $errMsg = "exit success";
            $_SESSION['rid'] = '1';
        } else {
            $errMsg = "update fail";
        }

        return response()->json([
            'errCode' => $errCode,
            'errMsg' => $errMsg
        ]);
    }
}
