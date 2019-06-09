/**
 * 1.websocket对象
 * 2.用户列表
 * 3.本机key（发送者）
 * 4.他机key（接收者）
 */
var ws = false;
var users = {};
var sKey;
var rKey = 'all';
var size;

function start() {
    // 创建socket
    ws = new WebSocket('ws://127.0.0.1:8080');

    // 握手监听
    ws.onopen = function () {
        // 状态1时握手成功
        if (ws.readyState == 1) {
            ws.send("type=enter&name=" + user.name + "&style=" + user.style + "&uid=" + user.uid + "&rid=" + user.rid + "&rname=" + user.rname);
            console.log('握手成功');
        }
    }

    // 失败
    ws.onclose = function () {
        ws = false;
        alert('WebSocket连接断开');
    }

    ws.onmessage = function (msg) {
        var data = JSON.parse(msg.data);
        switch (data['type']) {
            case 'exist':
                init2(data);
                break;
            case 'enter':
                enterRoom(data);
                break;
            case 'exit':
                exitRoom(data);
                break;
            case 'msg':
                post(data);
                break;
        }
    }
}

function push() {
    var text = $('#talk textarea').val();
    ws.send("type=msg&rKey=" + rKey + "&data=" + text);

    rKey = 'all';
    $('#talk textarea').attr('placeholder', "140字以内").val('');
}

$(function () {
    $('#clear').click(function () {
        rKey = 'all';
        $('#talk textarea').attr('placeholder', "140字以内").val('');
    });

    $('#post').find('button').click(function () {
        push();
    });
});

function showList() {
    $('#user_list').toggle();
}

// ajax init
function init1(responce) {
    var data = responce.data;
    user.name = data['name'];
    user.style = data['style'];
    user.uid = data['uid'];
    user.rid = data['rid'];
    user.rname = data['rname'];
    size = data['size'];
    document.title = user.rname + " | DOLLARS - 聊天室";
}

// websocket init
function init2(data) {
    // 修改基本信息
    sKey = data['key'];
    users = data['userlist'];
    $('#room').text(users.length + " / " + size + " | " + user.rname + " | " + user.name);

    // 添加信息
    msg_div('exist', data['user']['name']);

    // 填充成员列表
    var list = $('#user_list');
    for (var i = 0; i < users.length; i++) {
        var div = $('<div class="mem"></div>');
        div.attr('key', users[i]['key']).attr('id', "mem_" + i).attr('uid', users[i]['id']).attr('name', users[i]['name']);
        $('<div class="layui-badge-dot"></div>').css('background', users[i]['style']).appendTo(div);
        $('<span></span>').text(users[i]['name']).appendTo(div);
        list.append(div);
    }
    // 修改rKey和textarea
    $('.mem').click(function () {
        rKey = $(this).attr('key');
        $('#talk textarea').attr('placeholder', " @ " + $(this).attr('name'));
    });
}

// 进入房间
function enterRoom(data) {
    msg_div('enter', data['user']['name']);

    var i = users.length;
    users[i] = {
        'id': data['user']['id'],
        'key': data['sKey'],
        'name': data['user']['name'],
        'style': data['user']['style']
    };

    $('#room').text(users.length + " / " + size + " | " + user.rname + " | " + user.name);

    var div = $('<div class="mem"></div>');
    div.attr('key', users[i]['key']).attr('id', "mem_" + i).attr('uid', users[i]['id']).attr('name', users[i]['name']);
    $('<div class="layui-badge-dot"></div>').css('background', users[i]['style']).appendTo(div);
    $('<span></span>').text(users[i]['name']).appendTo(div);
    $('#user_list').append(div);

    // 修改rKey和textarea
    $('.mem').unbind('click');
    $('.mem').click(function () {
        rKey = $(this).attr('key');
        $('#talk textarea').attr('placeholder', " @ " + $(this).attr('name'));
    });
}

// 退出房间
function exitRoom(data) {

    // 修改users
    var i;
    for (i = 0; i < users.length; i++) {
        if (users[i]['key'] == data['sKey']) {
            // 添加信息
            msg_div('exit', users[i]['name']);
            users.splice(i, 1);
            break;
        }
    }
    $('#room').text(users.length + " / " + size + " | " + user.rname + " | " + user.name);
    var id = "#mem_" + i;
    $(id).remove();
}


// 发言div模板
function talk_div(data, sKey, time, rKey) {
    var box = $('#talk_box');
    // 发言人
    var u;
    for (var i = 0; i < users.length; i++) {
        if (users[i]['key'] == sKey) {
            u = users[i];
            break;
        }
    }

    var div = $('<div class="talk"></div>');
    
    var src = AVATAR + style[u['style']] + ".png";
    var left = $('<div class="left"></div>')
    $('<img src="" alt="">').attr('src', src).appendTo(left);
    $('<span></span>').text(u['name']).appendTo(left);
    div.append(left);

    var right = $('<div class="right"></div>');
    $('<div class="data"></div>').text(data).appendTo(right);
    $('<span class="time"></span>').text(time).appendTo(right);
    if (rKey != 'all') {
        $('<span class="layui-badge-dot"></span>').css('background', 'black').prependTo(right.find('.time'));
    }
    right.css('background', u['style']);
    div.append(right);

    box.append(div);
}

// 消息div模板
function msg_div(type, name) {
    var div = $('<div class="message"></div>');
    $('<div class="left"><i class="fa fa-caret-right" aria-hidden="true"></i><i class="fa fa-caret-right" aria-hidden="true"></i></div>').appendTo(div);
    var text;
    if (type == 'enter' || type == 'exist') {
        text = "加入";
    } else if (type == 'exit') {
        text = "离开";
    } else {
        return false;
    }
    $('<div class="right"></div>').text("@" + name + " | " + text + "了房间。").appendTo(div);
    $('#talk_box').append(div);
    return true;
}

function post(data) {

    talk_div(data['data'], data['sKey'], data['time'], data['rKey']);
}
