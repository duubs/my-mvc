<?php

namespace fastphp;

// 框架根目录
defined('CORE_PATH') or define('CORE_PATH', __DIR__);

/**
 * fastphp框架核心
 */
class Fastphp
{
    // 配置内容
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    // 运行程序
    public function run()
    {
        //spl_autoload_register()注册给定的函数作为 __autoload 的实现
        spl_autoload_register(array($this, 'loadClass'));
        $this->setReporting();// 检测开发环境
        $this->removeMagicQuotes();// 检测敏感字符并删除
        $this->unregisterGlobals();// 检测自定义全局变量并移除。
        $this->setDbConfig();// 配置数据库信息
        $this->route();// 路由处理
    }

    // 路由处理
    public function route()
    {
        $controllerName = $this->config['defaultController']; //默认控制器
        $actionName = $this->config['defaultAction']; // 默认方法
        $param = array(); // 获取URL参数

        // $url = $_SERVER['REQUEST_URI'];
        $url = $_SERVER['PATH_INFO'];
        
        // 清除?之后的内容
        $position = strpos($url, '?');

        //substr() 函数返回字符串的一部分。
        $url = $position === false ? $url : substr($url, 0, $position);
        // 删除前后的“/”
        $url = trim($url, '/');

        if ($url) {
            // 使用“/”分割字符串，并保存在数组中
            $urlArray = explode('/', $url);
            // 删除空的数组元素
            $urlArray = array_filter($urlArray);
            
            // 获取控制器名
            $controllerName = ucfirst($urlArray[0]);
            
            // 获取动作名
            array_shift($urlArray);//array_shift() 函数删除数组中第一个元素，并返回被删除元素的值
            
            $actionName = $urlArray ? $urlArray[0] : $actionName;
            
            // 获取URL参数
            array_shift($urlArray);
            
            $param = $urlArray ? $urlArray : array();
        }

        // 判断控制器和操作是否存在
        $controller = 'app\\controllers\\'. $controllerName . 'Controller';
        if (!class_exists($controller)) {
            exit($controller . '控制器不存在');
        }
        if (!method_exists($controller, $actionName)) {
            exit($actionName . '方法不存在');
        }

        // 如果控制器和操作名存在，则实例化控制器，因为控制器对象里面
        // 还会用到控制器名和操作名，所以实例化的时候把他们俩的名称也
        // 传进去。结合Controller基类一起看
        $dispatch = new $controller($controllerName, $actionName);

        // $dispatch保存控制器实例化后的对象，我们就可以调用它的方法，
        // 也可以像方法中传入参数，以下等同于：$dispatch->$actionName($param)
        call_user_func_array(array($dispatch, $actionName), $param);
    }

    // 检测开发环境
    public function setReporting()
    {
        if (APP_DEBUG === true) {
            //PHP 有诸多错误级别，使用该函数可以设置在脚本运行时的级别。如果没有设置可选参数 level，error_reporting() 仅会返回当前的错误报告级别。
            error_reporting(E_ALL); // 报告所有错误
            //PHP ini_set用来设置php.ini的值，在函数执行的时候生效，脚本结束后，设置失效。无需打开php.ini文件，就能修改配置，对于虚拟空间来说，很方便。 
            //display_errors 错误回显，一般常用语开发模式，但是很多应用在正式环境中也忘记了关闭此选项。错误回显可以暴露出非常多的敏感信息，为攻击者下一步攻击提供便利。推荐关闭此选项。 
            ini_set('display_errors','On'); //设置php.ini配置文件中的,display_errors,为开启状态下,若出现错误,则报错,出现错误提示 
        } else {
            error_reporting(E_ALL);
            //关闭状态下，若出现错误，则提示：服务器错误。但是不会出现错误提示
            ini_set('display_errors','Off');
            //错误日志的记录，可以帮助开发人员或者 管理人员查看系统是否存在问题。 如果需要将程序中的错误报告写入错误日志中，只要在PHP的配置文件中，将配置指令log_errors开启即可
            //在正式环境下用这个就行了，把错误信息记录在日志里。正好可以关闭错误回显
            ini_set('log_errors', 'On');
        }
    }

    // 删除敏感字符
    public function stripSlashesDeep($value)
    {
        // is_array()函数用于检测变量是否是数组 
        // array_map函数将用户自定义函数作用到数组中的每个值上，并返回用户自定义函数作用后的带有新值的数组。 stripslashes()删除反斜杠
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    // 检测敏感字符并删除
    public function removeMagicQuotes()
    {
        //get_magic_quotes_gpc — 获取当前 magic_quotes_gpc 的配置选项设置
        //php中的 magic_quotes_gpc 是配置在php.ini中的，他的作用类似addslashes()，就是对输入的字符创中的字符进行转义处理。他可以对$_POST、$__GET以及进行数据库操作的sql进行转义处理，防止sql注入。
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET ) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    // 检测自定义全局变量并移除。因为 register_globals 已经弃用，如果
    // 已经弃用的 register_globals 指令被设置为 on，那么局部变量也将
    // 在脚本的全局作用域中可用。 例如， $_POST['foo'] 也将以 $foo 的
    // 形式存在，这样写是不好的实现，会影响代码中的其他变量。 相关信息，
    // 参考: http://php.net/manual/zh/faq.using.php#faq.register-globals
    
    public function unregisterGlobals()
    {
        //register_globals 的意思就是注册为全局变量，所以当On的时候，传递过来的值会被直接的注册为全局变量直接使用，而Off的时候，我们需要到特定的数组里去得到它。
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    // 配置数据库信息
    public function setDbConfig()
    {
        if ($this->config['db']) {
            define('DB_HOST', $this->config['db']['host']);
            define('DB_NAME', $this->config['db']['dbname']);
            define('DB_USER', $this->config['db']['username']);
            define('DB_PASS', $this->config['db']['password']);
        }
    }

    // 自动加载类
    public function loadClass($className)
    {
        $classMap = $this->classMap();

        var_dump(APP_PATH.$className.'.php');
        echo "<br>";

        if (isset($classMap[$className])) {
            
            // 包含内核文件
            $file = $classMap[$className];
            //strpos() 函数查找字符串在另一字符串中第一次出现的位置。
        } elseif (strpos($className, '\\') !== false) {

            // 包含应用（application目录）文件
            // str_replace() 函数以其他字符替换字符串中的一些字符（区分大小写）。
            $file = APP_PATH . str_replace('\\', '/', $className) . '.php';
            if (!is_file($file)) {
                return;
            }
        } else {
            return;
        }

        include $file;

        // 这里可以加入判断，如果名为$className的类、接口或者性状不存在，则在调试模式下抛出错误
    }

    // 内核文件命名空间映射关系
    protected function classMap()
    {
        return [
            'fastphp\base\Controller' => CORE_PATH . '/base/Controller.php',
            'fastphp\base\Model'      => CORE_PATH . '/base/Model.php',
            'fastphp\base\View'       => CORE_PATH . '/base/View.php',
            'fastphp\db\Db'           => CORE_PATH . '/db/Db.php',
            'fastphp\db\Sql'          => CORE_PATH . '/db/Sql.php',
        ];
    }
}