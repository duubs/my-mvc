<?php 
/**
 *  入口文件
 */

header("content-type:text/html;charset=utf-8"); // 字符集编码

define('ROOT_DIR', dirname(__FILE__)); // 定义根路径

require 'lib/Controller.php'; // 引入文件

$obj = new Controller; // 实例化类

echo $obj->init(); // 初始化

 ?>