<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Group
{
    public function add()
    {
        $param = Request::instance()->param();
        Db::table('group')->insert($param);
        $group_id = Db::name('group')->getLastInsID();
        $result = Db::table('group')->where('group_id',$group_id)->find();
        return $result;
    }

    public function select()
    {
        $group_list = Db::table('group')->field('group_id,group_name')->order('group_create_time')->select();
        foreach($group_list as &$val){
            $val['tasks'] =  Db::table('group')->where('group_id',$val['group_id'])->select();
        }
        return $group_list;
    }

    public function update()
    {
        $param = Request::instance()->param();
        $result = Db::table('group')->where('group_id',$param['group_id'])->update(['group_name'=>$param['group_name']]);
        return $result;
    }

    public function delete($group_id='')
    {
        return Db::table('group')->delete($group_id);
    }
    
}
