<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return 'webcome';
    }

    public function update()
    {
        $re =  push_msg('all',4,'程序更新');
        var_dump($re);exit;
    }

}
