$(function () {

    // 初始化
    // 主题
    $('body').css('background', user.style);
    // 头像
    var avatar = AVATAR + style[user.style] + '.png';
    $('#avatar img').attr('src', avatar);
    // 名字
    $('#name span').text(user.name);
    // 杂项
    var height = window.innerHeight;
    $('#container').css('min-height', height);
});

function setStyle(response) {
    user.style = response.style;
    user.name = response.name;
    user.uid = response.uid;
    // 主题
    $('body').css('background', user.style);
    // 头像
    var avatar = AVATAR + style[user.style] + '.png';
    $('#avatar img').attr('src', avatar);
    // 名字
    $('#name span').text(user.name);
}

function setList(response) {
    /**
     * 1.在线状态`
     * 2.房间列表`
     */

    // 在线
    $('#nav span').text(response.rCount + " rooms - " + response.uCount + "users");

    // 房间列表
    /**
     * 1.房名`
     * 2.房间描述`
     * 3.房主名`
     * 4.最大在线`
     * 5.当前在线`
     * 6.成员名`
     * -----图形-----
     * 7.音乐开关`
     * 8.房主style`
     * 9.成员style`
     */

    for (var list in response.lists) {

        // 主体
        var room = response.lists[list];
        // 容器
        var item = $('<div class="room layui-row layui-col-space10"></div>');

        // 房间名字
        var roomname = $('<div class="tooltip layui-col-md4"></div>');
        roomname.append($('<i class="fa fa-music" aria-hidden="true"></i>'));
        if (room.open) {
            roomname.children('i').css('color', 'orange');
        }
        $('<span class="rname"></span>').text(room.roomname).attr('rid', room.roomid).appendTo(roomname);
        $('.rname').hover(function () {
            $(this).css('color', user.style);
        }, function () {
            $(this).css('color', '#000');
        });
        // 房间描述
        var description = $('<div class="tooltiptext"></div>').text(room.description);
        roomname.append(description);

        // 房主名字
        var ownername = $('<div class="layui-col-md4"></div>');
        $('<div class="layui-badge-dot"></div>').css('background', room.owner[0].style).appendTo(ownername);
        $('<span></span>').text(room.owner[0].name).appendTo(ownername);

        // 人数进度条
        var count = $('<div class="tooltip layui-col-md4"></div>');
        var percent = room.count / room.size * 100;
        var progress = $('<div class="progress"></div>').text(room.count + " / " + room.size);
        $('<div class="progress-bar"></div>').css('width', percent + "%").css('background', room.owner[0].style).appendTo(progress);
        progress.attr('percent', parseInt(percent));
        count.append(progress);
        // 成员列表
        var members = $('<div class="tooltiptext"></div>');
        $('<div class="member-title">在线成员列表</div>').appendTo(members);
        for (var member in room.members) {
            var mem = $('<div class="mem"></div>');
            $('<div class="layui-badge-dot"></div>').css('background', room.members[member].style).appendTo(mem);
            $('<span></span>').text(room.members[member].name).appendTo(mem);
            members.append(mem);
        }
        count.append(members);

        item.append(roomname).append(ownername).append(count);
        $('#list').append(item);
    }

}
