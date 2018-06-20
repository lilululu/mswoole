<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/19/18
 * Time: 3:49 PM
 */


$server=new swoole_http_server('0.0.0.0',9507);

$server->on('request',function($request,$response){

//    $server=$request->server;
//
//    if($server['request_uri']=="/favicon.ico"){
//
//
//        $response->end('123');
//    }else{

        $mysql=new mysqli('127.0.0.1','root','walMl12%','purchase');


        $res=$mysql->query('select * from fc_purchase limit 1');


        while($row=$res->fetch_row()){


            $list[]=$row;
        }


        $starttime=date('Ymd H:i:s',$list[0][1]);
//        $starttime='20180903';

        $response->end('9507 start time is '.$starttime);
//    }




});


$server->start();