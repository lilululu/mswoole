<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/12/18
 * Time: 1:19 PM
 */

$http=new swoole_http_server('0.0.0.0',9503);



function index(){

    return function ($request){

        $get=$request->get;


        return '<h1>hello this is index and a='.$get['a'].'</h1>';
    };


}

function login(){

    return function($request){

        $get=$request->get;

        return '<h1>hello this is login and username='.$get['username'].' and verify='.$get['yzm'].'</h1>';

    };
}

$handlers=array(

    '/index'=>index(),
    '/login'=>login(),
);

$http->on('request',function(swoole_http_request $request,swoole_http_response $response) use ($handlers){

    $server=$request->server;

    if($server['request_uri']=="/favicon.ico"){


        $response->end('123');
    }else{

        $res='<h1>hello world</h1>';

        if(array_key_exists($server['request_uri'],$handlers)){

            $res=$handlers[$server['request_uri']]($request);
        }

        $response->end($res);
    }




});



$http->start();