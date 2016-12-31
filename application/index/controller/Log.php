<?php
namespace app\index\controller;
use \think\Request;
class Log extends Base
{

    public function select()
    {
        $page = input('page');
        return model('Log')->select($page);
    }

}
