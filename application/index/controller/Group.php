<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Group extends Base
{
    public function add()
    {
        $param = Request::instance()->param();
        $param['user_id'] = $this->user_id;
        $param['group_create_time'] = dateline();
        $param['group_parter'] = json_encode($param['group_parter']);
        Db::table('group')->insert($param);
        $group_id = Db::name('group')->getLastInsID();
        $result = Db::table('group')->where('group_id',$group_id)->find();
        return $result;
    }

    public function select()
    {
        $where['group.user_id'] = $this->user_id;
        $group_list = Db::table('group')->join('user','group.user_id=user.user_id')->where($where)->order('group_create_time')->select();
        foreach($group_list as &$val){
            $val['group_parter'] = (array)json_decode($val['group_parter']);
        }
        $where2['group.user_id'] = array('<>',$this->user_id);
        $where2['group_parter'] = array('like','%"'.$this->user_id.'"%');
        $group_list2 = Db::table('group')->join('user','group.user_id=user.user_id')->where($where2)->order('group_create_time')->select();
        foreach($group_list2 as &$val){
            $val['group_parter'] = (array)json_decode($val['group_parter']);
        }
        return array_merge($group_list,$group_list2);
    }

    public function update()
    {
        $param = Request::instance()->param();
        $param['group_parter'] = json_encode($param['group_parter']);
        $result = Db::table('group')->where('group_id',$param['group_id'])->update($param);
        return $result;
    }

    public function delete($group_id='')
    {
        return Db::table('group')->delete($group_id);
    }
    
}
