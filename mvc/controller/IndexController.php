<?php 
namespace controller;
use lib\Controller;
use lib\Model;
use lib\View;

/**
* 
*/

class IndexController extends Controller
{
	public function index()
	{
		var_dump(M('aaa')->sel());
		echo "<br>";
		$this->assign(['a'=>'b']);
		$this->display('aa',['c'=>'d']);
		echo "我是方法";	
	}	
}


 ?>