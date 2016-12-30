<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function success($info){
    return ['status'=>1,'info'=>$info];
}

function error($info){
    return ['status'=>0,'info'=>$info];
}

function json($status,$info=''){
    header('Content-Type: application/json');
    echo json_encode(['status'=>$status,'info'=>$info]);
    exit;
}

function dateline(){
    return date('Y-m-d H:i:s');
}

function push_msg($uid,$type,$msg,$time=0)
{
    $data['uid'] = $uid;
    $data['time'] = $time;
    $data['data'] = array(
        'type'=>$type,
        'msg'=>$msg,
    );
    try{
        // 建立socket连接到内部推送端口
        $client = stream_socket_client('tcp://127.0.0.1:5678', $errno, $errmsg, 1);
        // 推送的数据，包含uid字段，表示是给这个uid推送
        // 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
        fwrite($client, json_encode($data)."\n");
        // 读取推送结果
        $re = fread($client, 8192);
        if($re == 'ok'){
            return false;
        }else{
            return true;
        }
    }catch(\Exception $e){
        
    }

}