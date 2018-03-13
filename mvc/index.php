<?php 
/**
 *  入口文件
 */

header("content-type:text/html;charset=utf-8"); // 字符集编码

define('ROOT_DIR', dirname(__FILE__)); // 定义根路径

require ROOT_DIR.'/lib/Function.php'; // 引入 框架自定义函数与自动加载 文件

$obj = new \lib\Controller; // 实例化类

$obj->init(); // 初始化

 ?>