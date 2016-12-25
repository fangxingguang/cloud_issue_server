<?php
namespace app\index\controller;
use think\Controller;

class Base extends controller
{
    public function _initialize()
    {
        $user = session('user');
        if(!$user){
           json(-1);
        }
        $this->user = session('user');
        $this->user_id = session('user.user_id');
    }

}
