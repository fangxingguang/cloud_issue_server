<?php
namespace app\index\controller;
use app\index\model\PushLog;
use \think\Request;
class Log extends Base
{

    public function select()
    {
        $page = input('page');
        return model('Log')->select($page);
    }

    public function pushSelect()
    {
        $page = input('page');
        $list = PushLog::order('create_time','desc')->page($page,20)->select();
        return $list;
    }

}
