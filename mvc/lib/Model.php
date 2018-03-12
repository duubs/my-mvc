<?php 

/**
* model 基类
*/
class Model
{
	
	public $config; // 配置文件
	public $table_name; // 表名
	public $pdo; // pdo对象
	public $where = ' WHERE 1=1 '; // where 条件
	public static $table_fields; //表字段名

	function __construct()
	{
		$this->config = require CONFIG_DIR.'/config.php';// 引入配置文件
		$this->pdo = new PDO('mysql:host='.$this->config['db']['host'].';dbname='.$this->config['db']['dbname'].';',$this->config['db']['username'],$this->config['db']['password']);	// 初始化一个PDO对象
	}

	// where 条件
	public function where($arr=[])
	{
		$arr = $this->redun($arr);
		foreach ($arr as $k => $v) {
			$this->where .= " AND `".$k."`='".$v."'";
		}
		return $this;
	}

	// 添加 完成之后返回新增数据id 否则 false
	public function add($arr = [])
	{
		$arr = $this->redun($arr);
		$keys = '`'.implode(array_keys($arr), '`,`').'`'; // 拼接字段
		$vals = "'".implode(array_values($arr), "','")."'"; // 拼接添加数据
		
		$sql = "INSERT INTO ".$this->table_name."(".$keys.") VALUES(".$vals.");";
		$list = $this->pdo->exec($sql);
		return $list ? $this->pdo->lastInsertId() : false;
	}

	// 删除
	public function del($arr = [])
	{
		$arr = $this->redun($arr);
		if (!empty($arr)) {	$this->where($arr);}
		$sql = "DELETE FROM ".$this->table_name.$this->where;
		return $this->pdo->exec($sql);
	}
	
	// 修改 参1 要修改的字段 参2 where条件
	public function up($arr = [],$where = '')
	{
		$arr = $this->redun($arr);
		if (!empty($where)) {	$this->where($where);}
		$sql = "UPDATE ".$this->table_name." SET ";
		foreach ($arr as $k => $v) {
			$sql .= "`".$k."`='".$v."',";
		}
		$sql = trim($sql,',').$this->where;
		return $this->pdo->exec($sql);
	}

	// 查询
	public function sel($arr=[])
	{
		if (!empty($arr)) {	$this->where($arr);}
		$sql = 'select * from '.$this->table_name.$this->where;	// mysql语句
		$list = $this->pdo->query($sql);			// 执行MySQL语句
		$list->setFetchMode(PDO::FETCH_ASSOC);		// 设置获取结果集的返回值的类型-关联数组形式
		return $list->fetchAll();					// 提取结果集的内容	
	}

	//获取当前表字段
	public function field(){
		if (!empty(self::$table_fields[$this->table_name])) {
			return self::$table_fields[$this->table_name];
		}
	
		$stmt = $this->pdo->query('desc '.$this->table_name);
		return self::$table_fields[$this->table_name] = $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	//过滤冗余字段
	public function redun($data=[]){
		$field = $this->field();
		var_dump(self::$table_fields);exit;
		foreach ($data as $k => $v) {
			if (!in_array($k,$field)) {
				unset($data[$k]);
			}
		}
		return $data;
	}
}

 ?>