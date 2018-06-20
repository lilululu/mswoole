<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/19/18
 * Time: 10:36 AM
 */

$server=new swoole_server('0.0.0.0',9505,SWOOLE_BASE,SWOOLE_SOCK_TCP);

$server->on('connect',function($serv,$fd){

    echo 'client:'.$fd.' connected';

});

$server->on('receive',function($serv,$fd,$reactor_id,$data){

    echo 'receive from '.$fd.' :'.$data;

    $res='who are u';

    $serv->send($fd,$res);

});

$server->start();