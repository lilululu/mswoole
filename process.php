<?php
//Swoole\Process::daemon();
//ini_set('default_socket_timeout', -1);
//swoole 进程池体验
//开启两个进程读取redis队列的内容，速度更快，分别读取，不会重复
$workernum=2;
$pool=new Swoole\Process\Pool($workernum);
$pool->on('WorkerStart',function($pool,$workerId){
    error_log( 'worker '.$workerId.' is start'."\r\n",3,'/var/www/html/swoole/log/process.log');
    $redis=new Redis();
    $redis->pconnect('127.0.0.1',6379);
    $keys='test_list';
    while(true){
        $task=$redis->brpop($keys,0);
//        echo "i am ".$workerId." and i get ".$task[1]."\n";
        error_log( "i am ".$workerId." and i get ".$task[1]."\r\n",3,'/var/www/html/swoole/log/process.log');
    }
});

$pool->on('WorkerStop',function($pool,$workerId){
//    echo "worker ".$workerId.' stopped';
    error_log( "worker ".$workerId.' stopped'."\r\n",3,'/var/www/html/swoole/log/process.log');

});

$pool->start();
