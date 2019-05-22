<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-22
 * Time: 9:56
 */
//利用协程 channel 实现MySQL 连接池
use Swoole\Coroutine\MySQL;
use Swoole\Coroutine as co;
class MysqlPool{
    private static $instance;
    private $pool;
    private $config;

    /**
     * @param array $config
     * @return static
     * 获取连接池实例
     */
    static function getInstance($config=[]){
        if(empty(self::$instance)){
            if(empty($config)){
                throw new RuntimeException('mysql config empty');
            }
            self::$instance=new static($config);
        }
        return self::$instance;
    }
    function __construct($config)
    {
        if(empty($this->pool)){
           $this->config=$config;
//           创建协程通道
           $this->pool=new co\Channel($config['pool_size']);
           for($i=0;$i<$config['pool_size'];$i++){
               $mysql=new MySQL();
               $res=$mysql->connect($config);
               if($res == false){
//                throw new RuntimeException('mysql connect failed');
                die('mysql connect failed');
               }else{
                   $this->put($mysql);
               }
           }
        }
    }
    function put($mysql){
        $this->pool->push($mysql);
    }
    function get(){
//        获取一个MySQL链接
        $mysql=$this->pool->pop($this->config['pool_get_timeout']);
        if($mysql == false){
            throw new RuntimeException('get mysql failed');
        }
        return $mysql;
    }
    function getLenth(){
        return $this->pool->length();
    }


}
