<?php 
/**
* 
*/

class IndexController extends Controller
{
	public function index()
	{
		// $Model = new Aaa;
		// $a = $Model->sel(['id'=>20]);
		var_export(M('aaa')->sel());
		// var_dump($a);
		// $this->assign(['a'=>'b']);
		// $this->display('aa',['c'=>'d']);
		echo "我是方法";	
	}	
}


 ?>