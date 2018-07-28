<?php

namespace app\index\controller;

use app\common\controller\BaseController;
use app\common\model\User;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return 'hi';
    }
}
