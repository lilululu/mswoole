<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/19/18
 * Time: 10:31 AM
 */

$server=new swoole_http_server('0.0.0.0',9504);

$server->on('request',function($request,$response){

//    $client=new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
//
//    $client->connect('127.0.0.1',9505);
//
//    $client->send('hello');
//
//    $res=$client->recv();

    $mysql=new Swoole\Coroutine\Mysql();

    $mysql->connect([

        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => 'walMl12%',
        'database' => 'purchase',
    ]);

    $mysql->setDefer();

    $mysql->query('select * from fc_purchase limit 1');

    $res=$mysql->recv();

    $starttime=date('Ymd H:i:s',$res[0]['starttime']);

    $response->end('9504 start time is '.$starttime);

});


$server->start();