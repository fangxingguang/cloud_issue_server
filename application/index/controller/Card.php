<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Card
{
    public function add()
    {
        $param = Request::instance()->param();
        Db::table('card')->insert($param);
        $card_id = Db::name('card')->getLastInsID();
        $result = Db::table('card')->where('card_id',$card_id)->find();
        return $result;
    }

    public function select()
    {
        $card_list = Db::table('card')->field('card_id,card_name')->order('card_order')->select();
        foreach($card_list as &$val){
            $val['tasks'] =  Db::table('task')->where('card_id',$val['card_id'])->select();
        }
        return $card_list;
    }

    public function update()
    {
        $param = Request::instance()->param();
        $result = Db::table('card')->where('card_id',$param['card_id'])->update(['card_name'=>$param['card_name']]);
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
    
}
