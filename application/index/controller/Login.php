<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Login
{
    public function index()
    {
        $param = Request::instance()->param();
        $where['user_name'] = $param['user_name'];
        $user = Db::table('user')->field('user_id,user_name')->where($where)->find();
        if($user){
            return success($user);
        }else{
            return error('用户不存在！');
        }
    }
    
}
