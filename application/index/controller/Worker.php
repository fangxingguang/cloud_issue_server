<?php
namespace app\index\controller;
use think\worker\Server;

class Worker extends Server
{
    protected $socket = 'websocket://0.0.0.0:2346';
    public $uidConnections = array();

    public function onMessage($connection,$data)
    {
        // 判断当前客户端是否已经验证,即是否设置了uid
        if(!isset($connection->uid))
        {
            // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
            $connection->uid = $data; //TODO
            /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
                * 实现针对特定uid推送数据
                */
            $this->uidConnections[$connection->uid] = $connection;
        }
        // 其它逻辑，针对某个uid发送 或者 全局广播
        // 假设消息格式为 uid:message 时是对 uid 发送 message
        // uid 为 all 时是全局广播
        list($recv_uid, $message) = explode(':', $data);
        // 全局广播
        if($recv_uid == 'all')
        {
            $this->broadcast($message);
        }
        // 给特定uid发送
        else
        {
            $this->sendMessageByUid($recv_uid, $message);
        }
    }

    // 当有客户端连接断开时
    public function onClose($connection)
    {
        if(isset($connection->uid))
        {
            // 连接断开时删除映射
            unset($this->uidConnections[$connection->uid]);
        }
    }

    // 向所有验证的用户推送数据
    public function broadcast($message)
    {
    foreach($this->uidConnections as $connection)
    {
            $connection->send($message);
    }
    }

    // 针对uid推送数据
    public function sendMessageByUid($uid, $message)
    {
        if(isset($this->uidConnections[$uid]))
        {
            $connection = $this->uidConnections[$uid];
            $connection->send($message);
        }
    }

}
