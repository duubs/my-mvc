<?php 
/**
* 基类 加载 控制器
*/

include ROOT_DIR.'/lib/Function.php'; // 引入 框架自定义函数与自动加载 文件

class Controller
{
	public $config = []; // 配置文件
	public static $view; // view类对象

	/**
	 * 初始化
	 */
	public function init()
	{
		define('CONTROLLER_DIR', ROOT_DIR.'/controller'); // controller 文件目录
		define('MODEL_DIR', ROOT_DIR.'/model'); // model 文件目录
		define('VIEW_DIR', ROOT_DIR.'/view'); // view 文件目录
		define('CONFIG_DIR', ROOT_DIR.'/config'); // config 文件目录
		
		$this->config = require CONFIG_DIR.'/config.php';// 引入配置文件
		self::$view = new View; // 设置视图对象
		$this->route(); // 设置路由
		$this->controller_action(CONTROLLER_NAME,ACTION_NAME); // 初始化控制器方法
	}

	// 初始化控制器方法
	public function controller_action($controller,$action)
	{
		// 首字母大写
		$controller = ucfirst($controller.'Controller');
		// 判断文件是否存在
		$file_name = CONTROLLER_DIR.'/'.$controller.".php";
		if (!file_exists($file_name)) {
			exit('<h2>文件不存在</h2>');
		}

		// 判断类是否存在
		if (!class_exists($controller)) {
			exit('<h2>类名与文件名不一致</h2>');
		}

		$class_obj = new $controller; // 实例化类
		
		// 判断方法是否存在
		if (!method_exists($class_obj,$action)) {
			exit('<h2>类内没有此方法</h2>');
		}

		// 此函数 自动调用类内方法
		call_user_func(array($class_obj,$action));

	}

	// 路由处理
    public function route()
    {
    	//判断是否使用默认参数
    	if (empty($_SERVER['PATH_INFO'])) {
    		$arr[] = $this->config['default_controller'];
    		$arr[] = $this->config['default_action'];
    	}else{
    		$_SERVER['PATH_INFO'] = trim($_SERVER['PATH_INFO'],'/');
    		$arr = explode('/',$_SERVER['PATH_INFO']);
    		$arr[1] = empty($arr[1]) ? $this->config['default_action'] : $arr[1];
    	}
		
		// 设置 控制器 与 方法
		define('CONTROLLER_NAME',$arr[0]);
        define('ACTION_NAME',$arr[1]);    	
	}

	// 渲染页面
	public function display($html='',$arr=[])
	{
		self::$view->display($html,$arr);
	}

	// 渲染变量
	public function assign($arr)
	{
		self::$view->assign($arr);
	}
}
 ?>