<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/20/18
 * Time: 2:25 PM
 */

$str="OK:a:1:{i:0;a:1:{s:14:\"Tables_in_test\";s:4:\"li_a\";}}";

$arr=explode(":",$str,2);

var_dump($arr);