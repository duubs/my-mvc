<?php 

function M($table_name)
{
	$table_name = ucfirst($table_name); // 首字母大写
	static $table_obj = []; // 对象集合
	//判断集合内是否有 本章表,没有添加进去
	if (!isset($table_obj[$table_name])) {
		$table_names = '\\model\\'.$table_name;
		$table_obj[$table_name] = new $table_names;
	}
	return $table_obj[$table_name]; 
}

//自动加载文件
function __autoload($class)
{
	include_once ROOT_DIR.'/'.str_replace('\\', '/', $class).'.php';
}








