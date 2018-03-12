<?php 

function M($table_name)
{
	$table_name = ucfirst($table_name); // 首字母大写
	static $table_obj = []; // 对象集合
	//判断集合内是否有 本章表,没有添加进去
	if (!isset($table_obj[$table_name])) {
		$table_obj[$table_name] = new $table_name;
	}
	return $table_obj[$table_name]; 
}

//自动加载文件
function __autoload($class)
{
	$arra = [
		ROOT_DIR.'/lib/'.$class.'.php',
		ROOT_DIR.'/controller/'.$class.'.php',
		ROOT_DIR.'/view/'.$class.'.php',
		ROOT_DIR.'/model/'.$class.'.php',
	];
	foreach ($arra as $k => $v) {
		if (file_exists($v)) {
			include $v;
		}
	}

}








