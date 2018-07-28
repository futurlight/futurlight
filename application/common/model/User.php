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
    protected $table_name = 'user';

    protected function columns()
    {
        return [self::increments('id', '主键id'),
                self::string('admin', '账号'),
                self::string('password', '密码', 255),
                self::string('email', '邮箱', 80),
                self::int('gender', '性別', 2),
                self::text('content', '内容'),
                self::datetime('login_time', '登录'),
                self::timestamp('date_time', '时间')
        ];
    }
}