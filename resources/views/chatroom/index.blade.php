<?php
session_start();
?>

<!-- 判断是否登录 -->
<?php
if (isset($_SESSION['name'])) {
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

    <title>DOLLARS - 聊天室</title>

    <!-- icon -->
    <link rel="Shortcut Icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />

    <!-- Scripts -->
    <script src="{{ asset('js/dependent.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>

<body>

    <div id="container" class="layui-fluid">
        <div id="logo" class="layui-row">
            <img src="favicon.ico" alt="">
        </div>

        <div id="name" class="layui-row">
            <span>USERNAME：</span>
            <input type="text">
        </div>

        <div id="setting" class="layui-row">
            <a href="javascript:setStyle()">SETTING</a>
        </div>

        <div id="enter" class="layui-row">
            <button onclick="enterLounge()">ENTER</button>
        </div>

        <div id="avatar">
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
    // 上传昵称进入大厅
    function enterLounge() {
        // 未填写昵称
        if ($('#name input').val() == '') {
            alert('请输入昵称');
            return false;
        } else if ($('#name input').val().length > 16) {
            alert('昵称不大于16位');
            $('#name input').val('');
            return false;
        } else {
            // 更改昵称
            user.name = $('#name input').val().substr(0, 16);

            // 上传昵称查重
            $.ajax({
                type: "post",
                url: "{{ url('login') }}",
                data: user,
                dataType: "json",
                success: function(response) {
                    console.log(response.errMsg);
                    if (response.errCode == 200) { // 不重复
                        window.location.href = "{{ url('lounge') }}";
                    } else { // 重复
                        $('#name input').val('');
                        user.name = null;
                        alert('昵称重复');
                    }
                }
            });
        }
    }
</script>

</html>