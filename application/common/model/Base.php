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
    protected $column       = [];
    protected $before_field = [];
    private   $key          = 'id';

    //protected $commit = "create";
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->column = $this->columns();
        $this->tableField();
    }

    protected function columns()
    {
        return [];
    }

    protected function tableField()
    {
        if (empty($this->table_name)) {
            return;
        }
        $prefix           = Config::get('database.prefix');
        $this->table_name = $prefix . $this->table_name;
        if (!$this->issetTable()) {
            $sql = 'create table `' . $this->table_name . '`(id int(11) AUTO_INCREMENT,PRIMARY KEY(`id`),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL)ENGINE=InnoDB DEFAULT CHARSET=UTF8';
            Db::query($sql);
        }
        $this->getNewField();
        //exit(json_encode($this->column));
        foreach ($this->column as $key => $item) {
            if ($item['tmp'] === '') {
                if ($key === 0) {
                    $after = $this->key;
                } else {
                    $after = $this->column[$key - 1]['name'];
                }
                Db::query("ALTER TABLE `{$this->table_name}` ADD `{$item['name']}` {$item['type']} NOT NULL DEFAULT '{$item['default']}' COMMENT '{$item['comment']}' AFTER $after;");
            } else {
                if (!$item['tmp']) {
                    Db::query("ALTER TABLE `{$this->table_name}` change `{$item['before_name']}` `{$item['name']}` {$item['type']} NOT NULL DEFAULT '{$item['default']}' COMMENT '{$item['comment']}';");
                    if ($item['key'] == 'PRI') {
                        $this->key = $item['name'];
                    }
                }
            }
        }
    }

    protected static function string($name, $comment = '', $length = 128, $default = null)
    {
        return self::generateType('VARCHAR', $name, $comment, $length, $default);
    }

    protected static function int($name, $comment = '', $length = 11, $default = 0)
    {
        return self::generateType('INT', $name, $comment, $length, $default);
    }

    protected static function increments($name = 'id', $comment = '')
    {
        return self::generateType('INT', $name, $comment, 11, 0, 'PRI');
    }

    protected static function text($name, $comment = '')
    {
        return self::generateType('TEXT', $name, $comment, null, null);
    }

    protected static function datetime($name, $comment = '', $default = '0000-00-00 00:00:00')
    {
        return self::generateType('DATETIME', $name, $comment, 0, $default);
    }

    protected static function timestamp($name, $comment = '', $default = '0000-00-00 00:00:00')
    {
        return self::generateType('TIMESTAMP', $name, $comment, 0, $default);
    }

    protected static function generateType($type, $name, $comment, $length, $default, $key = "")
    {
        $vo['name']    = $name;
        $vo['comment'] = $comment;
        $vo['default'] = $default;
        $type          = strtolower($type);
        switch ($type) {
            case 'varchar':
            case 'int':
                $type = $type . "($length)";
                break;
        }
        $vo['type'] = $type;
        $vo['key']  = $key;
        return $vo;
    }

    private function issetTable()
    {
        $database = Config::get('database.database');
        $table    = Db::table('information_schema.TABLES')->field('table_name')->where(['TABLE_SCHEMA' => $database,
                                                                                        'table_name'   => $this->table_name
        ])->select();
        if (empty($table)) {
            return false;
        }
        return true;
    }

    private function getTheBeforeField()
    {
        $database              = Config::get('database.database');
        $table_name            = $this->table_name;
        $where['table_name']   = $table_name;
        $where['table_schema'] = $database;
        $where['column_name']  = ['not in', 'updated_at,created_at'];
        // $where['column_name'] = ['NEQ', 'created_at'];
        $table = Db::table('information_schema.columns')->field(["column_name as name",
                                                                 "column_comment as comment",
                                                                 "column_default as 'default'",
                                                                 "column_type as 'type'",
                                                                 "COLUMN_KEY as 'key'"
        ])->where($where)->select();
        //exit($table);
        return $table;
    }

    /**
     * 获取新字段
     */
    private function getNewField()
    {
        $before_field       = $this->getTheBeforeField();
        $this->before_field = $before_field;
        //exit(json_encode($this->column));
        foreach ($this->column as $key => $item) {
            $this->column[$key]['tmp'] = '';
            foreach ($before_field as $vo) {
                if ($vo['name'] == $item['name']) {
                    if (!array_diff($item, $vo)) {
                        $this->column[$key]['tmp'] = true;
                    } else {
                        $this->column[$key]['before_name'] = $vo['name'];
                        $this->column[$key]['tmp']         = false;
                    }
                }
                if ($item['key'] == 'PRI' && $vo['key'] == 'PRI') {
                    $this->column[$key]['before_name'] = $vo['name'];
                    $this->column[$key]['tmp']         = false;
                }
            }
        }
        // exit(json_encode($this->column));
    }
}