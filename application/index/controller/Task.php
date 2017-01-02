<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Task extends Base
{
    public function add()
    {
        $param = Request::instance()->param();
        $param['task_create_time'] = date('Y-m-d H:i:s');
        $task_id = Db::table('task')->insertGetId($param);
        add_log('新建任务：'.$param['task_name']);
        $result = Db::table('task')->where('task_id',$task_id)->find();
        return $result;
    }

    public function select()
    {
        $task_list = Db::table('task')->field('task_id,task_name')->order('task_create_time desc')->select();
        foreach($task_list as &$val){
            $val['tasks'] =  Db::table('task')->where('task_id',$val['task_id'])->select();
        }
        return $task_list;
    }

    public function update()
    {
        $param = Request::instance()->param();
        $result = Db::table('task')->where('task_id',$param['task_id'])->update($param);
        add_log('更新任务：'.$param['task_name']);
        return $result;
    }

    public function delete($task_id='')
    {
        $where['task_id'] = $task_id;
        $task= Db::table('task')->where($where)->find();
        $result = Db::table('task')->delete($task_id);
        add_log('删除任务：'.$task['task_name']);
        return $result;
    }

     public function move()
    {
        $param = Request::instance()->param();
        $where['task_id'] = $param['task_id'];
        $task= Db::table('task')->where($where)->find();

        $to_card = Db::table('card')->where('card_id',$param['to_card_id'])->find();
        if($to_card['card_owner']){
            $card_owner_arr = json_decode($to_card['card_owner']);
            push_msg('all',1,'任务流程变更！');
            push_msg($card_owner_arr,2,'小伙，到你啦！');
            push_msg('all',3,['group_id'=>$to_card['group_id']]);
        }
        $result = Db::table('task')->where('task_id',$param['task_id'])->update(['card_id'=>$param['to_card_id']]);
        add_log('移动任务：'.$task['task_name']);
        return $result;
    }
    
}
