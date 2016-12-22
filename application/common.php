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