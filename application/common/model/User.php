<?php
/**
 * Created by PhpStorm.
 * User: c8755
 * Date: 2018/7/23
 * Time: 22:16
 */

namespace app\common\model;

class User extends Base
{
    public function columns()
    {
        $this->tableField('user', [$this->increments(),
            $this->string('admin'),
            $this->string('password'),
            $this->timestamps()
        ]);
    }
}