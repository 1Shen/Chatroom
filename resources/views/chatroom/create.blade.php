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

    <title>创建房间 | DOLLARS - 聊天室</title>

    <!-- icon -->
    <link rel="Shortcut Icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />

    <!-- Scripts -->
    <script src="{{ asset('js/dependent.js') }}"></script>
    <script src="{{ asset('js/create.js') }}"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/create.css') }}">
</head>

<body>
    <div class="layui-container">
        <div class="layui-row">
            <div class="layui-col-md2" style="height: 200px">
            </div>
            <form action="" id="container" class="layui-col-md8">
                <div class="layui-row">
                    <div class="layui-col-md2">
                        <div>房间名称</div>
                    </div>
                    <div class="layui-col-md10">
                        <input id="name" type="text" required placeholder="请输入名称（20字以内）" autocomplete="off" maxlength="20">
                    </div>
                </div>
                <div class="layui-row">
                    <div class="layui-col-md2">
                        <div>房间描述</div>
                    </div>
                    <div class="layui-col-md10">
                        <textarea id="description" placeholder="请输入内容（50字以内）" maxlength="50"></textarea>
                    </div>
                </div>
                <div class="layui-row">
                    <div class="layui-col-md2">
                        <div>房间人数</div>
                    </div>
                    <div class="layui-col-md10">
                        <select id="size">
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </div>
                <div class="layui-row">
                    <div class="layui-col-md2">
                        <div><i class="fa fa-music" aria-hidden="true"></i></div>
                    </div>
                    <div class="layui-col-md10">
                        <select id="open">
                            <option value="0">关</option>
                            <option value="1">开</option>
                        </select>
                    </div>
                </div>
                <div id="button" class="layui-row">
                    <button onclick="createRoom()">立即提交</button>
                    <button id="reset" type="reset">重置</button>
                </div>
            </form>
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
    function createRoom() {

        var message = {
            'name': null,
            'description': null,
            'size': 0,
            'open': 0
        }

        message.name = $('#name').val();
        message.description = $('#description').val();
        message.size = $('#size').val();
        message.open = $('#open').val();

        $.ajax({
            type: "post",
            url: "{{ url('create') }}",
            data: message,
            dataType: "json",
            success: function(response) {
                console.log(response.errMsg);
                $('#reset').trigger('click');
            }
        });
    }
</script>

</html>