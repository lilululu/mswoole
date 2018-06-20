<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/11/18
 * Time: 3:37 PM
 */

//$server=new swoole_websocket_server('127.0.0.1',9502);
$server=new swoole_websocket_server('0.0.0.0',9502);

$server->on('open', function (swoole_websocket_server $server, $frame) {
    //每一次客户端连接 最大连接数将增加
    $message = "欢迎 连接号{$frame->fd}：进入了聊天室";
    echo $message."\n";
    foreach ($server->connections as $key => $value) {
        if($frame->fd != $value){
            $server->push($value, $message);
        }
    }
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    $fd   = $frame->fd;
    $data = $frame->data;
    $message = "[连接号{$fd}]:{$data}";
    //向所有人广播
    foreach ($server->connections as $key => $value) {
        if($frame->fd != $value){
            $server->push($value, $message);
        }
    }
});

$server->on('close', function (swoole_websocket_server $server, $fd) {
    //关闭连接 连接减少
    $message = "连接号{$fd}：退出了聊天室";
    echo "client {$fd} closed\n";
    foreach ($server->connections as $key => $value) {
        if($fd != $value){
            $server->push($value, $message);
        }
    }
});
$server->start();