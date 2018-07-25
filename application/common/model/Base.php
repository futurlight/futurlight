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

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->columns();
    }

    public function columns()
    {
        return [

        ];
    }

    protected function tableField($table_name, $field = [])
    {
        if (empty($table_name)) {
            return;
        }
        $this->table_name = $table_name;
        $prefix = Config::get('database.prefix');
        if (!$this->issetTable()) {
            $sql = implode(',', $field);
            $sql = 'create table `' . $prefix . $table_name . '`(' . $sql . ')ENGINE=InnoDB DEFAULT CHARSET=UTF8';
            Db::query($sql);
        }
    }

    protected function string($name, $commit = '', $length = 128, $default = '')
    {
        return $this->generateType('VARCHAR', $name, $commit, $length, $default);
    }

    protected function increments($name = 'id')
    {
        $vo['name'] = $name;
        $vo['type'] = 'int';
        $this->column[] = $vo;
        return "`{$name}` INT(11) AUTO_INCREMENT,PRIMARY KEY(`{$name}`)";
    }

    protected function timestamps()
    {
        $this->column[] = 'created_at';
        $this->column[] = 'updated_at';
        $vo['name'] = $name;
        $vo['length'] = $length;
        if (!empty($commit)) {
            $vo['commit'] = $commit;
            $commit = "commit '{$commit}'";
        }
        if (!empty($default)) {
            $vo['default'] = $default;
            $default = "default '{$default}'";
        }
        $this->column[] = $vo;
        return "`created_at` TIMESTAMP ,`updated_at` TIMESTAMP ";
    }

    protected function generateType($type, $name, $commit, $length, $default)
    {
        $sql = '';
        $vo['name'] = $name;
        $vo['length'] = $length;
        if (!empty($commit)) {
            $vo['commit'] = $commit;
            $commit = "commit '{$commit}'";
        }
        if (!empty($default)) {
            $vo['default'] = $default;
            $default = "default '{$default}'";
        }
        $this->column[] = $vo;
        switch ($type) {
            case 'VARCHAR':
                $sql = "`{$name}` VARCHAR({$length}) " . $commit . " " . $default;
                break;
        }
        return $sql;
    }

    private function issetTable()
    {
        $database = Config::get('database.database');
        $table = Db::table('information_schema.TABLES')->field('table_name')->where(['TABLE_SCHEMA' => $database, 'table_name' => $this->table_name])->select();
        //  $table    = Db::query("SELECT table_name FROM information_schema.`TABLES` WHERE TABLE_SCHEMA='{$database}' AND table_name ='{$this->table_name}'");
        if (empty($table)) {
            return false;
        }
        return true;
    }


    private function getTheBeforeField()
    {
        $database = Config::get('database.database');
        $table = Db::table('information_schema.columns')->field(["column_name as name", "column_comment as comment", "column_default as 'default'", "data_type as 'type'", "CHARACTER_MAXIMUM_LENGTH as 'length'"])
            ->where(['table_name' => "{$this->table_name}", 'table_schema' => "{$database}"])->select();
        return $table;
    }


    /**
     * 是否为新字段
     */
    private function isANewField()
    {
        $before_field = $this->getTheBeforeField();
        foreach ($before_field as $item) {

        }
    }

}