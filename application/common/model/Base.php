<?php
/**
 * Created by PhpStorm.
 * User: c8755
 * Date: 2018/7/23
 * Time: 21:25
 */

namespace app\common\model;

use think\Config;
use think\Db;
use think\Model;

class Base extends Model
{
    protected $table_name;
    protected $column = [];
    //protected $commit = "create";
    private $sql = [];

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->columns();
    }

    public function columns()
    {
        return;
    }

    protected function tableField($table_name, $field = [])
    {
        if (empty($table_name)) {
            return;
        }
        $this->table_name = $table_name;
        if (!$this->issetTable()) {
           // exit(json_encode($field));
            $sql = implode(',', $field);
            exit($sql);
        }
    }

    protected function string($name, $commit = '', $length = 128, $default = '')
    {
        return $this->generateType('VARCHAR', $name, $commit, $length, $default);
    }

    protected function increments($name = 'id')
    {
        $this->column[] = $name;
        return "`{$name}` INT(11) AUTO_INCREMENT";
    }

    protected function timestamps()
    {
        $this->column[] = 'created_at';
        $this->column[] = 'updated_at';
        return "`created_at` TIMESTAMP ,`updated_at` TIMESTAMP ";
    }

    protected function generateType($type, $name, $commit, $length, $default)
    {
        $this->column[] = $name;
        $sql            = '';
        switch ($type) {
            case 'VARCHAR':
                $sql = "`{$name}` VARCHAR({$length}) commit '{$commit}' default '{$default}'";
                break;
        }
        return $sql;
    }

    private function issetTable()
    {
        $database = Config::get('database.database');
        $table    = Db::query("SELECT table_name FROM information_schema.`TABLES` WHERE TABLE_SCHEMA='{$database}' AND table_name ='{$this->table_name}'");
        if (empty($table)) {
            return false;
        }
        return true;
    }
}