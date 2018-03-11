<?php 
/**
* 
*/
include ROOT_DIR.'/lib/Model.php'; // 引入 框架自定义函数与自动加载 文件

class IndexController extends Controller
{
	public function index()
	{
		$Model = new Model;
		$a = $Model->where()->sel(['id'=>20]);
		var_dump($a);
		// $this->assign(['a'=>'b']);
		// $this->display('aa',['c'=>'d']);
		echo "我是方法";	
	}	
}


 ?>