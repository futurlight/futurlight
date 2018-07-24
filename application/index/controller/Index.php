<?php

namespace app\index\controller;

use app\common\controller\BaseController;
use app\common\model\User;

class Index extends BaseController
{
    public function index()
    {
        $model = new User();
        $model->columns();
    }
}
