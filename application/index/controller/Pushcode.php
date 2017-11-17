<?php
namespace app\index\controller;
use \think\Request;
use \think\Db;
class Pushcode extends Base
{
    public function index()
    {
        $param = Request::instance()->param();
        $where['card_id'] = $param['card_id'];
        $card= Db::table('card')->where($where)->find();
        $card_owner_arr = json_decode($card['card_owner'],true);
        $this->user_limit($card_owner_arr);

        switch($param['push_address']){
            case 'test1' : $cmd = '/opt/deploy/git/git_branch_test'.' '.$param['branch_name'];break;
            case 'test2' : $cmd = '/opt/deploy/git/git_branch_test2'.' '.$param['branch_name'];break;
            case 'pre' : $cmd = '/opt/deploy/git/rsync_www_pre.sh';break;
            case 'pro' : $cmd = '/opt/deploy/git/rsync_www_pro.sh';break;
            case 'pre-backend' : $cmd = 'su - yunwei -c "bash /home/yunwei/release_wuxipre.sh'.' '.$param['branch_name'].'"';break;
            case 'pro-backend-wuxi' : $cmd = 'su - yunwei -c "bash /home/yunwei/wuxipro_release.sh'.' '.$param['branch_name'].'"';break;
            case 'pro-backend-beijing' : $cmd = 'su - yunwei -c "bash /home/yunwei/bjpro_release.sh'.' '.$param['branch_name'].'"';break;
        }

//        if (!file_exists($cmd)) {
//            return success('未找到发布脚本！');
//        }

        exec($cmd,$array);
        return success(implode("<br/>",$array));
    }

   

}
