<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Card extends Base
{
    public function add()
    {
        $param = Request::instance()->param();

        $where['group_id'] = $param['group_id'];
        $group= Db::table('group')->where($where)->find();
        $this->user_limit([$group['user_id']]);

        $param['user_id'] = $this->user_id;
        $card_id = Db::table('card')->insertGetId($param);
        add_log('新建流程：'.$param['card_name']);
        $result = Db::table('card')->where('card_id',$card_id)->find();
        return $result;
    }

    public function select()
    {
        $param = Request::instance()->param();
        $where['group_id'] = $param['group_id'];
        $card_list = Db::table('card')->where($where)->order('card_order')->select();
        foreach($card_list as &$val){
            $val['card_owner'] = json_decode($val['card_owner']);
            $val['tasks'] =  Db::table('task')->join('user','task.user_id = user.user_id')->where('card_id',$val['card_id'])->select();
        }
        return $card_list;
    }

    public function update()
    {
        $param = Request::instance()->param();
        $where['card_id'] = $param['card_id'];
        $card = Db::table('card')->where($where)->find();

        $this->user_limit([$card['user_id']]);

        if(isset($param['card_owner'])){
            $param['card_owner'] = json_encode($param['card_owner']);
        }
        $result = Db::table('card')->where('card_id',$param['card_id'])->update($param);
        add_log('更新流程：'.$card['card_name']);
        return success($result);
    }

    public function delete($card_id='')
    {
        $where['card_id'] = $card_id;
        $card= Db::table('card')->where($where)->find();

        $this->user_limit([$card['user_id']]);

        $result = Db::table('card')->delete($card_id);
        add_log('删除流程：'.$card['card_name']);

        Db::table('task')->where('card_id',$card_id)->delete();

        return success($result);
    }

    public function order()
    {
        $param = Request::instance()->param();

        $where['card_id'] = $param['order'][0]['card_id'];
        $card= Db::table('card')->where($where)->find();

        $this->user_limit([$card['user_id']]);

        foreach($param['order'] as $val){
            Db::table('card')->where('card_id',$val['card_id'])->update(['card_order'=>$val['card_order']]);
        }
    }

    public function owner_select()
    {
        $param = Request::instance()->param();
        $where['card_id'] = $param['card_id'];
        $card= Db::table('card')->field('card_id,card_owner')->where($where)->find();
        $card['card_owner'] = (array)json_decode($card['card_owner']);
        return $card;
    }
    
}
