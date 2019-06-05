<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// 涉及在线表和用户表
use App\User;
use App\Online;

session_start();

class UserController extends Controller
{
    public function login(Request $request)
    {
        // 登录

        // 检测是否登录
        $id = DB::table('users')
            ->where('name', $request->input('name'))
            ->value('id');
        $check = DB::table('onlines')
            ->where('uid', $id)
            ->count();

        if ($check == 0) {

            // 添加用户
            $user = User::create([
                'name' => $request->input('name'),
                'style' => $request->input('style')
            ]);

            $online = Online::create([
                'uid' => $user['id'],
                'rid' => 1
            ]);

            $_SESSION['name'] = $user['name'];
            $_SESSION['style'] = $user['style'];
            $_SESSION['uid'] = $user['id'];
            $_SESSION['rid'] = 1;
            $_SESSION['rname'] = 'lounge';

            // 返回信息
            return response()->json([
                'errCode' => 200,
                'errMsg' => 'login success'
            ]);
        }
        return response()->json([
            'errCode' => 100,
            'errMsg' => 'login fail'
        ]);
    }

    public function logout()
    {
        // 退出

        if (!isset($_SESSION)) {
            return view('chatroom.index');
        }

        // 移出online
        $user = DB::table('onlines')
            ->where('uid', '=', $_SESSION['uid'])
            ->delete();

        // 清除session
        unset($_SESSION);
        session_destroy();

        // return response()->json([
        //     'errCode' => 200,
        //     'errMsg' => 'logout success'
        // ]);
        return view('chatroom.index');
    }
}
