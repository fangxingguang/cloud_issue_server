<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return 'webcome';
    }

    public function test()
    {
        $re =  push_msg('all',2,'测试');
        var_dump($re);exit;
    }

}
