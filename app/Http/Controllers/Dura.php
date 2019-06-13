<?php

namespace App\Http\Controllers;

session_start();

$servername = 'localhost';
$username = "root";
$password = "";
$conn = new \mysqli($servername, $username, $password, 'chatroom');

$dura = new Dura('127.0.0.1', 8080);

$dura->run();

class Dura
{
    public $master; // socket的resource
    public $sockets; // socket连接池
    public $users; // client的所有信息

    private $sda = array();   // 已接收的数据
    private $slen = array();  // 数据总长度
    private $sjen = array();  // 接收数据的长度
    private $message = array();    // 加密key
    private $n = array();

    // 构造函数
    public function __construct($address, $port)
    {
        // 创建socket并把保存socket资源在$this->master
        $this->master = $this->WebSocket($address, $port);
        // 创建socket连接池
        $this->sockets = array($this->master);
    }

    // 启动服务
    public function run()
    {
        // 直到socket断开为止
        while (true) {

            $changes = $this->sockets;
            $write = NULL;
            $except = NULL;

            /*

            //这个函数是同时接受多个连接的关键，它是为了阻塞程序继续往下执行。

            socket_select ($sockets, $write = NULL, $except = NULL, NULL);

            $sockets可以理解为一个数组，这个数组中存放的是文件描述符。当它有变化（就是有新消息到或者有客户端连接/断开）时，socket_select函数才会返回，继续往下执行。

            $write是监听是否有客户端写数据，传入NULL是不关心是否有写变化。

            $except是$sockets里面要被排除的元素，传入NULL是”监听”全部。

            最后一个参数是超时时间

            如果为0：则立即结束

            如果为n>1: 则最多在n秒后结束，如遇某一个连接有新动态，则提前返回

            如果为null：如遇某一个连接有新动态，则返回

            */

            socket_select($changes, $write, $except, NULL);

            foreach ($changes as $socket) {

                if ($socket == $this->master) {
                    // 如果有新client

                    /**
                     * 1.接受
                     * 2.分配id
                     * 3.存进连接池
                     * 4.记录信息
                     */

                    $client = socket_accept($this->master);
                    $key = uniqid();
                    $this->sockets[] = $client;

                    $this->users[$key] = array(
                        'socket' => $client,
                        'hand' => false
                    );
                } else {
                    // 判断信息长度，断开or正常
                    $length = 0;

                    $buffer = '';

                    //读取该socket的信息，注意：第二个参数是引用传参即接收数据，第三个参数是接收数据的长度
                    do {

                        $len = socket_recv($socket, $buf, 1024, 0);

                        $length += $len;

                        $buffer .= $buf;
                    } while ($len == 1024);

                    //根据socket在user池里面查找相应的$k,即键ID
                    $key = $this->search($socket);

                    // 1.断开连接
                    if ($length < 7) {
                        $this->close($key);
                        continue;
                    }

                    // 2.正常，判断是否握手
                    if (!$this->users[$key]['hand']) {
                        // 未握手
                        $this->handShake($key, $buffer);
                    } else {
                        // 已握手，发送信息
                        $buffer = $this->uncode($key, $buffer);

                        if (!$buffer) {
                            continue;
                        } else {
                            $this->send($key, $buffer);
                        }
                    }
                }
            }
        }
    }

    // 根据socket在users里面查找相应的$key
    public function search($socket)
    {
        foreach ($this->users as $key => $value) {

            if ($socket == $value['socket'])

                return $key;
        }

        return false;
    }

    // 握手
    public function handShake($k, $buffer)
    {
        // 截取Sec-WebSocket-Key的值并加密，其中$key后面的一部分258EAFA5-E914-47DA-95CA-C5AB0DC85B11字符串应该是固定的
        $buf  = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:') + 18);

        $key  = trim(substr($buf, 0, strpos($buf, "\r\n")));

        $new_key = base64_encode(sha1($key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));

        // 按照协议组合信息进行返回
        $new_message = "HTTP/1.1 101 Switching Protocols\r\n";

        $new_message .= "Upgrade: websocket\r\n";

        $new_message .= "Sec-WebSocket-Version: 13\r\n";

        $new_message .= "Connection: Upgrade\r\n";

        $new_message .= "Sec-WebSocket-Accept: " . $new_key . "\r\n\r\n";

        socket_write($this->users[$k]['socket'], $new_message, strlen($new_message));

        // 对已经握手的client做标志
        $this->users[$k]['hand'] = true;

        return true;
    }

    // 断开socket
    public function close($key)
    {
        // 推送登出信息
        $message['type'] = 'exit';
        $message['info'] = $this->users[$key]['user_info'];
        $this->pushMsg($key, $message, 'all');

        //断开相应socket
        socket_close($this->users[$key]['socket']);

        //删除相应的user信息
        unset($this->users[$key]);

        //重新定义sockets连接池
        $this->sockets = array($this->master);

        foreach ($this->users as $value) {

            $this->sockets[] = $value['socket'];
        }

        //输出日志
        $this->e("key:$key close");
    }

    // 解密
    public function uncode($key, $str)
    {
        $mask = array();

        $data = '';

        $msg = unpack('H*', $str);

        $head = substr($msg[1], 0, 2);

        if ($head == '81' && !isset($this->slen[$key])) {

            $len = substr($msg[1], 2, 2);

            $len = hexdec($len); // 把十六进制的转换为十进制

            if (substr($msg[1], 2, 2) == 'fe') {

                $len = substr($msg[1], 4, 4);

                $len = hexdec($len);

                $msg[1] = substr($msg[1], 4);
            } else if (substr($msg[1], 2, 2) == 'ff') {

                $len = substr($msg[1], 4, 16);

                $len = hexdec($len);

                $msg[1] = substr($msg[1], 16);
            }

            $mask[] = hexdec(substr($msg[1], 4, 2));

            $mask[] = hexdec(substr($msg[1], 6, 2));

            $mask[] = hexdec(substr($msg[1], 8, 2));

            $mask[] = hexdec(substr($msg[1], 10, 2));

            $s = 12;

            $n = 0;
        } else if ($this->slen[$key] > 0) {

            $len = $this->slen[$key];

            $mask = $this->ar[$key];

            $n = $this->n[$key];

            $s = 0;
        }

        $e = strlen($msg[1]) - 2;

        for ($i = $s; $i <= $e; $i += 2) {

            $data .= chr($mask[$n % 4] ^ hexdec(substr($msg[1], $i, 2)));

            $n++;
        }

        $dlen = strlen($data);

        if ($len > 255 && $len > $dlen + intval($this->sjen[$key])) {

            $this->ar[$key] = $mask;

            $this->slen[$key] = $len;

            $this->sjen[$key] = $dlen + intval($this->sjen[$key]);

            $this->sda[$key] = $this->sda[$key] . $data;

            $this->n[$key] = $n;

            return false;
        } else {

            unset($this->ar[$key], $this->slen[$key], $this->sjen[$key], $this->n[$key]);

            $data = $this->sda[$key] . $data;

            unset($this->sda[$key]);

            return $data;
        }
    }

    // 加密
    public function code($msg)
    {
        $frame = array();

        $frame[0] = '81';

        $len = strlen($msg);

        if ($len < 126) {

            $frame[1] = $len < 16 ? '0' . dechex($len) : dechex($len);
        } else if ($len < 65025) {

            $s = dechex($len);

            $frame[1] = '7e' . str_repeat('0', 4 - strlen($s)) . $s;
        } else {

            $s = dechex($len);

            $frame[1] = '7f' . str_repeat('0', 16 - strlen($s)) . $s;
        }

        $frame[2] = $this->ord_hex($msg);

        $data = implode('', $frame);

        return pack("H*", $data);
    }

    public function ord_hex($data)
    {
        $msg = '';

        $l = strlen($data);

        for ($i = 0; $i < $l; $i++) {

            $msg .= dechex(ord($data{
                $i}));
        }

        return $msg;
    }

    // 发送信息
    public function send($sKey, $buffer)
    {
        parse_str($buffer, $buf);

        $message = array();

        if ($buf['type'] == 'enter') {
            // 加入

            // 记录信息
            $room_info = array(
                'id' => $buf['rid'],
                'name' => $buf['rname']
            );
            $user_info = array(
                'id' => $buf['uid'],
                'name' => $buf['name'],
                'style' => $buf['style']
            );
            $this->users[$sKey]['room_info'] = $room_info;
            $this->users[$sKey]['user_info'] = $user_info;

            // 准备发送信息
            $message['type'] = 'enter';
            $message['user'] = $user_info;
            $rKey = 'all';

            // 日志
            $this->e("key:$sKey open");
        } else if ($buf['type'] == 'exit') {
            // 退出
            $this->close($sKey);
        } else {
            // 普通发送信息
            $message['type'] = $buf['type'];
            $message['data'] = $buf['data'];
            $rKey = $buf['rKey'];
        }

        // 推送信息
        $this->pushMsg($sKey, $message, $rKey);
    }

    // 推送信息
    public function pushMsg($sKey, $message, $rKey = 'all')
    {
        $send = $this->users[$sKey]['user_info'];
        $receive = $this->users[$rKey]['user_info']['id'];
        $room = $this->users[$sKey]['room_info']['id'];

        $message['send'] = $send['name'];
        $message['sKey'] = $sKey;
        $message['rKey'] = $rKey;
        $message['time'] = date('m-d H:i:s');

        $str = $this->code(json_encode($message));

        // 判断发送范围
        if ($rKey === 'all') {
            // 群聊

            $receive = 0;
            $message['receive'] = 'all';

            // 检索同房间的client
            $users = $this->getUsers($room);

            // 判断推送类型
            if ($message['type'] === 'enter') {
                // 加入
                $message['type'] = 'exist';
                $message['userlist'] = $this->getList($users);
                $message['key'] = $sKey;
                $message['receive'] = $sKey;

                // 单独对新client进行编码处理，数据不一样
                $str1 = $this->code(json_encode($message));

                // 对新client自己单独发送，因为有些数据是不一样的
                socket_write($this->users[$sKey]['socket'], $str1, strlen($str1));
                
                // 不需要发送给新client下一条
                unset($users[$sKey]);
            }
            // 普通及退出
            foreach ($users as $user) {
                socket_write($user['socket'], $str, strlen($str));
            }
        } else {
            // 私聊
            socket_write($this->users[$sKey]['socket'], $str, strlen($str));
            socket_write($this->users[$rKey]['socket'], $str, strlen($str));
        }

        // 插入数据库
        $this->insertDB($send['id'], $receive, $room, $message['data']);
    }

    // 传相应的IP与端口进行创建socket操作
    public function WebSocket($address, $port)
    {

        $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1); //1表示接受所有的数据包

        socket_bind($server, $address, $port);

        socket_listen($server);

        $this->e('Server Started : ' . date('Y-m-d H:i:s'));

        $this->e('Listening on   : ' . $address . ' port ' . $port);

        return $server;
    }

    // 获取同房间在线人员
    public function getUsers($room)
    {
        $users = array();

        foreach ($this->users as $key => $value) {
            if ($value['room_info']['id'] == $room) {
                $users[$key] = $value;
            }
        }

        return $users;
    }

    // 获取同房间在线人员信息
    public function getList($users)
    {
        $list = array();

        foreach ($users as $key => $value) {
            $list[] = array(
                'key' => $key,
                'id' => $value['user_info']['id'],
                'name' => $value['user_info']['name'],
                'style' => $value['user_info']['style']
            );
        }

        return $list;
    }

    // 插入数据库
    public function insertDB($send, $receive, $room, $message = ".")
    {
        $sql = "INSERT INTO discussions (send_id, receive_id, room_id, message)
            VALUES ('$send', '$receive', '$room', '$message')";
        global $conn;
        if ($conn) {
            $conn->query($sql);
        }
    }

    // 记录日志
    public function e($str)
    {
        $str = $str . "\n";
        // 编码处理
        echo iconv('utf-8', 'gbk//IGNORE', $str);
    }
}
