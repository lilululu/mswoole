<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/19/18
 * Time: 10:54 AM
 */

$client=new swoole_client(SWOOLE_SOCK_TCP);

$client->connect('127.0.0.1',9505);

$client->send("hello world\n");

$res=$client->recv();

$client->close();

var_dump($res);