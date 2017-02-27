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
        $param['user_id'] = $this->user_id;
        $task_id = Db::table('task')->insertGetId($param);
        add_log('新建任务：'.$param['task_name']);
        $result = Db::table('task')->where('task_id',$task_id)->find();
        $card = Db::table('card')->where('card_id',$result['card_id'])->find();
        push_msg('all',3,['group_id'=>$card['group_id']]);
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
        if(isset($param['task_file'])){
            $param['task_file'] = json_encode($param['task_file']);
        }
        $result = Db::table('task')->where('task_id',$param['task_id'])->update($param);
        add_log('更新任务：'.$param['task_name']);

        $card = Db::table('task')->join('card','task.card_id = card.card_id')->where('task_id',$param['task_id'])->find();
        push_msg('all',3,['group_id'=>$card['group_id']]);
        return $result;
    }

    public function delete($task_id='')
    {
        $where['task_id'] = $task_id;
        $task= Db::table('task')->where($where)->find();
        $card = Db::table('card')->where('card_id',$task['card_id'])->find();

        $card_owner_arr = json_decode($card['card_owner'],true);
        $this->user_limit($card_owner_arr);

        $result = Db::table('task')->delete($task_id);
        add_log('删除任务：'.$task['task_name']);
      
        push_msg('all',3,['group_id'=>$card['group_id']]);
        return success($result);
    }

    public function move()
    {
        $param = Request::instance()->param();
        $where['task_id'] = $param['task_id'];
        $task= Db::table('task')->where($where)->find();

        $card = Db::table('card')->where('card_id',$task['card_id'])->find();
        $card_owner_arr = json_decode($card['card_owner'],true);
        $this->user_limit((array)$card_owner_arr);

        $result = Db::table('task')->where('task_id',$param['task_id'])->update(['card_id'=>$param['to_card_id']]);
        $to_card = Db::table('card')->where('card_id',$param['to_card_id'])->find();
        if($to_card['card_owner']){
            $card_owner_arr = json_decode($to_card['card_owner'],true);
            push_msg($card_owner_arr,1,'你有新的任务啦！（'.$task['task_name'].'）');
            push_msg($card_owner_arr,2,'你有新的任务啦！（'.$task['task_name'].'）');
            push_msg('all',3,['group_id'=>$to_card['group_id'],'task_id'=>$task['task_id']]);
        }
        add_log('移动任务：'.$task['task_name']);
        return success($result);
    }
    
}
