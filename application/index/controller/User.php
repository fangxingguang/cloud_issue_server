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
        $user_id = Db::table('user')->insertGetId($param);
        $result = Db::table('user')->where('user_id',$user_id)->find();
        add_log('新建用户：'.$param['user_name']);
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
        add_log('更新用户：'.$param['user_name']);
        return $result;
    }

    public function delete($user_id='')
    {
        $where['user_id'] = $user_id;
        $user= Db::table('user')->where($where)->find();
        add_log('删除用户：'.$user['user_name']);
        return Db::table('user')->delete($user_id);
    }
    
}
