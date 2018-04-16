<?php

namespace app\index\controller;

use \think\Request;
use \think\Db;

class Job extends Base
{
    public function add()
    {
        $params = Request::instance()->param();
        if (empty($params['id'])) {
            $params['create_time'] = dateline();
            $params['update_time'] = dateline();
            Db::table('job')->insert($params);
        } else {
            $params['update_time'] = dateline();
            Db::table('job')->update($params);
        }
        return success('操作成功！');
    }

    public function select()
    {
        //查询未分组的
        $list1 = Db::table('job')->where(['group_id' => 0])->select();
        //分组查询
        $list2 = [];
        $groupList = Db::table('job_group')->select();
        foreach ($groupList as $group) {
            $taskList[0] = Db::table('job')->where(['group_id' => $group['id'],'status'=>0])->limit(10)->order('update_time','desc')->select();
            $taskList[1] = Db::table('job')->where(['group_id' => $group['id'],'status'=>1])->limit(10)->order('update_time','desc')->select();
            $taskList[2] = Db::table('job')->where(['group_id' => $group['id'],'status'=>2])->limit(10)->order('update_time','desc')->select();
            $list2[] = [
                'id' => $group['id'],
                'name' => $group['name'],
                'uid' => $group['uid'],
                'taskList' => $taskList
            ];
        }
        $result = [
            'list1' => $list1,
            'list2' => $list2,
        ];
        return success($result);
    }

    public function delete()
    {
        $params = Request::instance()->param();
        Db::table('job')->delete($params);
        return success('操作成功！');
    }

    public function groupAdd()
    {
        $params = Request::instance()->param();
        $user = Db::table('user')->where(['user_id' => $params['uid']])->find();
        $params['name'] = $user['user_name'];
        if (empty($params['id'])) {
            Db::table('job_group')->insert($params);
        } else {
            Db::table('job_group')->update($params);
        }
        return success('操作成功！');
    }

    public function groupDelete()
    {
        $params = Request::instance()->param();
        Db::table('job_group')->delete($params);
        return success('操作成功！');
    }

    public function move()
    {
        $params = Request::instance()->param();
        $params['update_time'] = dateline();
        Db::table('job')->update($params);

        $group = Db::table('job_group')->where('id',$params['group_id'])->find();

        push_msg($group['uid'],1,'你有新的工作啦！');
        push_msg('all',5,'');

        return success('操作成功！');
    }


}
