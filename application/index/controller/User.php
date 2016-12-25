<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class User extends Base
{
    public function add()
    {
        $param = Request::instance()->param();
        $param['user_create_time'] = dateline();
        Db::table('user')->insert($param);
        $user_id = Db::name('user')->getLastInsID();
        $result = Db::table('user')->where('user_id',$user_id)->find();
        return $result;
    }

    public function select()
    {
        $user_list = Db::table('user')->order('user_create_time desc')->select();
        return $user_list;
    }

    public function update()
    {
        $param = Request::instance()->param();
        $result = Db::table('user')->update($param);
        return $result;
    }

    public function delete($user_id='')
    {
        return Db::table('user')->delete($user_id);
    }
    
}
