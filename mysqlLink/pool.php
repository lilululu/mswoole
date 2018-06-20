<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/20/18
 * Time: 1:53 PM
 */

$serv=new swoole_server('0.0.0.0',9508);

$serv->set([

    'worker_num'=>4,

    'task_worker_num'=>3,
]);

function on_Receive($serv,$fd,$reactor_id,$sql){

    $result=$serv->taskwait($sql);


    list($status,$db_res)=explode(":",$result,2);


    if($status=="OK"){

        $db_res=var_export(unserialize($db_res),true);

    }

    $serv->send($fd,$db_res);


}


function on_Task($serv,$task_id,$worker_id,$sql){

    static $link=null;

    if(empty($link)){


        $link=new mysqli('127.0.0.1','root','walMl12%','test');

        if(mysqli_connect_error()){

            $link=null;

            $serv->finish("ER:".mysqli_connect_error());

            return;
        }
    }

    $result=$link->query($sql);

    if (!$result) {
        $serv->finish("ER:" . mysqli_error($link));
        return;
    }

    $data = $result->fetch_all(MYSQLI_ASSOC);
    $serv->finish("OK:" . serialize($data));
}

function my_onFinish($serv, $data)
{
    echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;

    echo "2222\n";

    var_dump($data);
}

$serv->on('Receive', 'on_Receive');
$serv->on('Task', 'on_Task');
$serv->on('Finish', 'my_onFinish');
$serv->start();