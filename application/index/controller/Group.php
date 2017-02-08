<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Group extends Base
{
    public function add()
    {
        $param = Request::instance()->param();
        $data['group_name'] = $param['group_name'];
        $data['group_des'] = $param['group_des'];
        $data['user_id'] = $this->user_id;
        $data['group_create_time'] = dateline();
        if(isset($param['group_parter'])){
            $data['group_parter'] = json_encode($param['group_parter']);
        }
         $group_id = Db::table('group')->insertGetId($data);
        add_log('新建项目：'.$data['group_name']);

        $result = Db::table('group')->join('user','group.user_id=user.user_id')->where('group_id',$group_id)->find();
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

        $where['group_id'] = $param['group_id'];
        $group= Db::table('group')->where($where)->find();
        $this->user_limit([$group['user_id']]);

        $param['group_parter'] = json_encode($param['group_parter']);
        $result = Db::table('group')->where('group_id',$param['group_id'])->update($param);
        add_log('更新项目：'.$param['group_name']);
        return success($result);
    }

    public function delete($group_id='')
    {
        $where['group_id'] = $group_id;
        $group= Db::table('group')->where($where)->find();
        $this->user_limit([$group['user_id']]);

        $result = Db::table('group')->delete($group_id);
        add_log('删除项目：'.$group['group_name']);

        $card_list = Db::table('card')->where('group_id',$group_id)->select();
        Db::table('task')->where('card_id','in',array_column($card_list,'card_id'))->delete();
        Db::table('card')->where('group_id',$group_id)->delete();
        
        return success($result);
    }
    
}
