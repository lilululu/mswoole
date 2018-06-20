<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/20/18
 * Time: 2:16 PM
 */

$client=new swoole_client(SWOOLE_SOCK_TCP);

$client->connect('127.0.0.1',9508);

$sql="select * from li_a";

$client->send($sql);

$res=$client->recv();


$client->close();

var_dump($res);