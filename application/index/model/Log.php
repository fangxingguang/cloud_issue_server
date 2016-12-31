<?php
namespace app\index\model;

use think\Model;

class Log extends Model
{

    public function add($info){
        $data['user_id'] = session('user.user_id');
        $data['log_info'] = $info;
        $data['log_create_time'] = dateline();
        db('log')->insert($data);
    }

    public function select($page=1){
        return db('log')->join('user','log.user_id = user.user_id')->order('log_create_time desc,log_id desc')->page($page,20)->select();
    }

}