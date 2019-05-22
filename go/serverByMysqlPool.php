<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-22
 * Time: 10:13
 */
require_once 'mysqlPool.php';
$config=[
    'host'=>'192.168.106.88',
    'port'=>3306,
    'user'=>'root',
    'password'=>'',
    'database'=>'sixFoot',
//    设置连接池数量
    'pool_size'=>3,
//    未取得链接时 超时时间
//   channel pop方法时设置超时时间
    'pool_get_timeout'=>0.5,
];
$http=new Swoole\Http\Server('0.0.0.0',9501);
//worker 进程启动时，初始化连接池
$http->on('WorkerStart',function($serv,$worker_id) use ($config){
    try{
        MysqlPool::getInstance($config);
    }catch (Exception $e){
        echo $e->getMessage()."\n";
        $serv->shutdown();
    }
});
$http->on('request',function($request,$response){
//    浏览器自动发的请求 忽略
    if ($request->server['path_info'] == '/favicon.ico') {
        $response->end('');
        return;
    }
    if($request->server['path_info'] == '/list'){
        go(function() use($request,$response){
            try{
//                获取连接池实例
                $pool=MysqlPool::getInstance();
//                获取MySQL链接
                $mysql=$pool->get();
//                defer 为延时处理，这里是用完后将MySQL链接归还至连接池
//                不明白，这是个快捷函数吗，看文档是 $server->defer(callback) 这么用的啊
                defer(function() use ($mysql){
                    MysqlPool::getInstance()->put($mysql);
                    echo '1 当前可用链接数量 '.MysqlPool::getInstance()->getLenth()."\n";
                });
                $result=$mysql->query("select * from sf_member where mobile='15733171013'");
                $response->end(json_encode($result));
            }catch (Exception $e){
                $response->end($e->getMessage());
            }

        });
        return;
    }
//    访问 timeout请求，同时在浏览器打开4个tab，同时请求
//    因为我们设置连接池只有三个链接，所以前三个页面会在10秒钟后返回，最后一个页面没有获取到链接，直接超时返回

    if($request->server['path_info'] == '/timeout'){
        go(function() use($request,$response){
//            获取url中的参数
            $str=$request->server['query_string'];
            try{
                $pool=MysqlPool::getInstance();
                echo '2 '.$str.'当前可用链接数量 '.MysqlPool::getInstance()->getLenth()."\n";
                $mysql=$pool->get();
                echo '3 '.$str.'当前可用链接数量 '.MysqlPool::getInstance()->getLenth()."\n";
                defer(function() use ($mysql,$str){
                    MysqlPool::getInstance()->put($mysql);
                    echo '4 '.$str.' 当前可用链接数量 '.MysqlPool::getInstance()->getLenth()."\n";
                });
                $result=$mysql->query("select * from sf_member where mobile='15733171013'");
                co::sleep(10);
                $response->end(json_encode($result));
            }catch (Exception $e){
                $response->end($e->getMessage());
            }

        });
        return;
    }

});

$http->start();