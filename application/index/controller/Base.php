<?php
namespace app\index\controller;
use think\Controller;
use \think\Db;
class Base extends controller
{
    public function _initialize()
    {
        $user = session('user');
        if(!$user){
           json(-1);
        }
        $this->user = session('user');
        $this->user_id = session('user.user_id');
    }
    
    public function user_limit($allow_user_arr)
    {
        $where['user_id'] = $this->user_id;
        $user= Db::table('user')->where($where)->find();

        if($user['user_name'] != 'admin' && !in_array($this->user_id,$allow_user_arr)){
            json(0,'权限不足！');
        }
    }

}
