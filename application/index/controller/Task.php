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
        Db::table('task')->insert($param);
        $task_id = Db::name('task')->getLastInsID();
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
        return $result;
    }

    public function delete($task_id='')
    {
        return Db::table('task')->delete($task_id);
    }

     public function move()
    {
        $param = Request::instance()->param();
        $result = Db::table('task')->where('task_id',$param['task_id'])->update(['card_id'=>$param['to_card_id']]);
        return $result;
    }
    
}
