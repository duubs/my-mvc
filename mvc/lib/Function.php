<?php 

//自动加载文件
function __autoload($class)
{
	include ROOT_DIR.'/lib/'.$class.'.php';
}








