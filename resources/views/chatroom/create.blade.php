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
            <div class="layui-col-md1" style="height: 200px">
            </div>
            <div id="container" class="layui-col-md10">
                <form class="layui-form">
                    <div class="layui-form-item">
                        <label class="layui-form-label">房间名称</label>
                        <div class="layui-input-block">
                            <input id="name" type="text" name="name" required lay-verify="required" placeholder="请输入名称（20字以内）" autocomplete="off" class="layui-input" maxlength="20">
                        </div>
                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">房间描述</label>
                        <div class="layui-input-block">
                            <textarea id="description" name="description" placeholder="请输入内容（50字以内）" class="layui-textarea" maxlength="50"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">房间人数</label>
                        <div class="layui-input-block">
                            <select id="size" name="size" lay-verify="required">
                                <option value=""></option>
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
                    <div class="layui-form-item">
                        <label class="layui-form-label"><i class="fa fa-music" aria-hidden="true"></i></label>
                        <div class="layui-input-block">
                            <select id="open" name="size" lay-verify="required">
                                <option value="o">关</option>
                                <option value="1">开</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="formDemo" onclick="createRoom()">立即提交</button>
                            <button id="reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                    <!-- <button onclick="createRoom()">aaa </button> -->
                </form>
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
    layui.use('form', function() {
        var form = layui.form;
    });

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
                if (response.errCode == 200) { // 创建成功
                    window.location.href = "{{ url('room') }}";
                } else { // 创建失败
                    alert('创建失败');
                    $('#reset').trigger('click');
                }
            }
        });
    }
</script>

</html>