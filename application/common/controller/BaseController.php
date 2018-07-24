<?php
/**
 * Created by PhpStorm.
 * User: c8755
 * Date: 2018/7/23
 * Time: 21:00
 */

namespace app\common\controller;

use think\Controller;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class BaseController extends Controller
{
    protected $ext;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->ext = Request::instance()->ext();
    }

    protected function displayByData($data = [], $template = '')
    {
        $data = ['code' => 0,
                 'msg'  => 'success',
                 'data' => $data,
        ];
        return $this->displayByArr($data, $template);
    }

    protected function displayBySuccess($msg = "操作成功！", $url = '')
    {
        $data['code'] = 0;
        $data['msg']  = $msg;
        $data['data'] = null;
        if ($this->isHtml()) {
            $this->success($msg, $url);
        }
        $this->displayByJson($data);
    }

    protected function displayByError($msg = "操作失败！", $code = -1, $data = [])
    {
        $data['code'] = $code;
        $data['msg']  = $msg;
        $data['data'] = null;
        if ($this->isHtml()) {
            $this->error($msg);
        }
        $this->displayByJson($data);
    }

    protected function displayByJson($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }

    protected function displayByArr($data, $template)
    {
        if ($this->isHtml()) {
            $this->displayTemplate($data, $template);
        }
        $this->displayByJson($data);
    }

    protected function displayTemplate($data, $template)
    {
        $result   = $this->fetch($template, $data);
        $response = Response::create($result, "html")->header([]);
        throw new HttpResponseException($response);
    }

    private function isHtml()
    {
        if ($this->ext == '' || $this->ext == 'html') {
            return true;
        }
        return false;
    }
}