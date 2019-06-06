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
    <a href="{{ url('exit') }}">array_diff_assoc</a>
</body>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

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
            setMessage(response);
        }
    });
</script>

</html>