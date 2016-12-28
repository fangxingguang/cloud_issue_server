<?php
use Workerman\Worker;
use \Workerman\Lib\Timer;

require_once './Workerman/Autoloader.php';
// 初始化一个worker容器，监听1234端口
$worker = new Worker('websocket://0.0.0.0:2346');
// 这里进程数必须设置为1
$worker->count = 1;
// worker进程启动后建立一个内部通讯端口
$worker->onWorkerStart = function($worker)
{
    // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
    $inner_text_worker = new Worker('Text://0.0.0.0:5678');
    $inner_text_worker->onMessage = function($connection, $buffer)
    {
        global $worker;
        // $data数组格式，里面有uid，表示向那个uid的页面推送数据
        $data = json_decode($buffer, true);
        //print_r($data);
        $uid = $data['uid'];
        $time = $data['time'];
        $message = json_encode($data['data']);
        // uid 为 all 时是全局广播
        // 全局广播
        if($uid == 'all'){
            if($time > 0){
                // n秒后执行，最后一个参数传递false，表示只运行一次
                Timer::add($time, 'broadcast', array($message), false);
                $ret = true;
            }else{
                $ret = broadcast($message);
            }
        }elseif(is_array($uid)){// 给多个uid发送
            if($time > 0){
                // n秒后执行，最后一个参数传递false，表示只运行一次
                Timer::add($time, 'sendMessageByUidArr', array($uid,$message), false);
                $ret = true;
            }else{
                 $ret = sendMessageByUidArr($uid,$message);
            }
        }else{// 给特定uid发送
            if($time > 0){
                // n秒后执行，最后一个参数传递false，表示只运行一次
                Timer::add($time, 'sendMessageByUid', array($uid,$message), false);
                $ret = true;
            }else{
                $ret = sendMessageByUid($uid, $message);
            }
        }
        // 返回推送结果
        $connection->send($ret ? 'ok' : 'fail');
    };
    $inner_text_worker->listen();
};
// 新增加一个属性，用来保存uid到connection的映射
$worker->uidConnections = array();
// 当有客户端发来消息时执行的回调函数
$worker->onMessage = function($connection, $data)use($worker)
{
    global $worker;
    // 判断当前客户端是否已经验证,既是否设置了uid
    if(!isset($connection->uid))
    {
       // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
       $connection->uid = $data;
       /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
        * 实现针对特定uid推送数据
        */
       $worker->uidConnections[$connection->uid] = $connection;
       //$connection->send('login');
       return;
    }
};

// 当有客户端连接断开时
$worker->onClose = function($connection)use($worker)
{
    global $worker;
    if(isset($connection->uid))
    {
        // 连接断开时删除映射
        unset($worker->uidConnections[$connection->uid]);
    }
};

// 向所有验证的用户推送数据
function broadcast($message)
{
   global $worker;
   foreach($worker->uidConnections as $connection)
   {
        $connection->send($message);
   }
   return true;
}

// 针对uid推送数据
function sendMessageByUid($uid, $message)
{
    global $worker;
    if(isset($worker->uidConnections[$uid]))
    {
        $connection = $worker->uidConnections[$uid];
        $connection->send($message);
        return true;
    }
    return false;
}

// 针对uid推送数据
function sendMessageByUidArr($uid_arr, $message)
{
    global $worker;
    foreach($uid_arr as $uid){
        if(isset($worker->uidConnections[$uid]))
        {
            $connection = $worker->uidConnections[$uid];
            $connection->send($message);
        }
    }
    return true;
}

// 运行所有的worker（其实当前只定义了一个）
Worker::runAll();