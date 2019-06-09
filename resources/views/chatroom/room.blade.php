<?php
session_start();
?>

<!-- 判断是否已经进入房间 -->
<?php
if ($_SESSION['rid'] == 1) {
    // =1在大厅
    $url = url('lounge');
    header("Location: $url");
    exit;
}
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>房间 | DOLLARS - 聊天室</title>

    <!-- icon -->
    <link rel="Shortcut Icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />

    <!-- Scripts -->
    <script src="{{ asset('js/dependent.js') }}"></script>
    <script src="{{ asset('js/room.js') }}"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/room.css') }}">
</head>

<body>
    <div id="post_box" class="layui-fluid layui-container">
        <div class="layui-col-md3" style="height: 150px"></div>
        <div class="layui-col-md6">
            <div id="title" class="layui-row">
                <span id="room"></span>
                <div id="menu">
                    <a href=""><i class="fa fa-music" aria-hidden="true"></i></a>
                    <a href="javascript:void(0);" onclick="showList()"><i class="fa fa-user" aria-hidden="true"></i></a>
                    <a href="{{ url('exit') }}"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
                </div>
            </div>
            <div id="talk" class="layui-row">
                <textarea maxlength="140" placeholder="140字以内" required="required"></textarea>
            </div>
            <div id="post" class="layui-row">
                <button>POST!</button>
            </div>
        </div>
        <div id="user_list">
            <div id="clear">清除@</div>
        </div>
    </div>

    <div id="talk_box" class="layui-container">
    </div>
</body>

<!-- websocket支持检测 -->
<script>
    if (typeof(WebSocket) == 'undefined') {
        alert('你的浏览器不支持 WebSocket ，推荐使用Google Chrome 或者 Mozilla Firefox');
    }
</script>

<script>
    $.ajax({
        type: "get",
        url: "{{ url('init') }}",
        dataType: "json",
        success: function(response) {
            init1(response);
            start();
        }
    });
</script>

</html>