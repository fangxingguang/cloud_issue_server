<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Card extends Base
{
    public function add()
    {
        $param = Request::instance()->param();
        $param['user_id'] = $this->user_id;
        Db::table('card')->insert($param);
        $card_id = Db::name('card')->getLastInsID();
        $result = Db::table('card')->where('card_id',$card_id)->find();
        return $result;
    }

    public function select()
    {
        $param = Request::instance()->param();
        $where['group_id'] = $param['group_id'];
        $card_list = Db::table('card')->field('card_id,card_name')->where($where)->order('card_order')->select();
        foreach($card_list as &$val){
            $val['tasks'] =  Db::table('task')->where('card_id',$val['card_id'])->select();
        }
        return $card_list;
    }

    public function update()
    {
        $param = Request::instance()->param();
        $param['card_owner'] = json_encode($param['card_owner']);
        $result = Db::table('card')->where('card_id',$param['card_id'])->update($param);
        return $result;
    }

    public function delete($card_id='')
    {
        return Db::table('card')->delete($card_id);
    }

    public function order()
    {
        $param = Request::instance()->param();
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
