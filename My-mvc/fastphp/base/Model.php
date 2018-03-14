<?php
namespace fastphp\base;

use fastphp\db\Sql;

class Model extends Sql
{
    protected $model;

    public function __construct()
    {
        // 获取数据库表名
        if (!$this->table) {

            // 获取模型类名称
            // get_class -- 返回对象的类名
            $this->model = get_class($this);

            // 删除类名最后的 Model 字符
            // substr() 函数返回字符串的一部分。
            $this->model = substr($this->model, 0, -5);

            // 数据库表名与类名一致
            // 把所有字符转换为小写：
            $this->table = strtolower($this->model);
        }
    }
}