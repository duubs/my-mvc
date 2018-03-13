<?php 
/**
* 视图基层
*/
namespace lib;

class View extends Controller
{

	public $param; //准备渲染的数据 

	// 渲染页面
	public function display($html='',$arr=[])
	{
		$file = empty($html)?ACTION_NAME:$html; // 判断加载文件名
		$file = VIEW_DIR.'/'.CONTROLLER_NAME.'/'.$file.'.php';//加载文件路径
		//验证视图是否存在
		if(!file_exists($file)){
			die('<h1 align="center">404 找不到视图 '.$file.'.html</h1>');
		}
		
		//合并渲染的数据
		if(!empty($arr)) $this->param = array_merge($this->param, $arr);
		
		//制作渲染的变量
		foreach ($this->param as $k => $v) {
			$$k = $v;
		}
		
		include $file; // 引入视图对应文件
	}

	// 渲染变量
	public function assign($arr)
	{
		$this->param = $arr; // 暂存变量内
	}
}


?>