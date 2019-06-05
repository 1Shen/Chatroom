<?php
session_start();
?>

<!-- 判断是否已经进入房间 -->
<?php
if ($_SESSION['rid'] != 1) {
    // =1在大厅
    $url = url('room');
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

    <title>大厅 | DOLLARS - 聊天室</title>

    <!-- icon -->
    <link rel="Shortcut Icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />

    <!-- Scripts -->
    <script src="{{ asset('js/dependent.js') }}"></script>
    <script src="{{ asset('js/lounge.js') }}"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/lounge.css') }}">
</head>

<body>
    <div id="container" class="layui-container">
        <div class="layui-row">
            <div id="list" class="layui-col-md7">
                <div id="nav" class="layui-row">
                    <a id="create" href="{{ url('create') }}">创建房间</a>
                    <a href="{{ url('lounge') }}">刷新</a>
                    <span>在线</span>
                </div>
                <div class="layui-row" style="height: 20px"></div>
            </div>

            <div class="layui-col-md1" style="height: 400px"></div>

            <div class="layui-col-md4">
                <div class="layui-row">
                    <div class="layui-col-md3">
                        <div id="avatar">
                            <img src="" alt="">
                        </div>
                    </div>
                    <div class="layui-col-md9">
                        <div class="layui-row">
                            <div id="name">
                                <span></span>
                            </div>
                        </div>
                        <div class="layui-row">
                            <div id="exit">
                                <a href="{{ url('logout') }}">登出</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-row">
                    <div id="search">
                        <input type="text" placeholder="搜索">
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

<script>
    $.ajax({
        type: "get",
        url: "{{ url('list') }}",
        dataType: "json",
        success: function(response) {
            setStyle(response);
            setList(response);
            $('.rname').each(function() {
                if ($(this).parent().parent().find('.progress').attr('percent') != 100) {
                    $(this).click(function() {
                        user.rid = $(this).attr('rid');
                        $.ajax({
                            type: "post",
                            url: "{{ url('enter') }}",
                            data: user,
                            dataType: "json",
                            success: function(response) {
                                console.log(response.errMsg);
                                if (response.errCode == 300) { // 已加入
                                    window.location.href = "{{ url('room') }}";
                                    console.log('yijiaru');
                                } else if (response.errCode == 200) { // 加入成功
                                    window.location.href = "{{ url('room') }}";
                                } else { // 加入失败
                                    alert('加入失败');
                                }
                            }
                        });
                    });
                } else {
                    $(this).attr('disabled', true);
                    $(this).css('pointer-events', 'none');
                    $(this).parent().hover(function() {
                        $(this).css('cursor', 'not-allowed');
                    }, function() {
                        $(this).css('cursor', 'pointer');
                    });
                }
            });
        }
    });
</script>

</html>