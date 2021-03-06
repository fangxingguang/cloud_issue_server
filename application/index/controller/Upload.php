<?php
namespace app\index\controller;

class Upload
{
    public function uploadImage()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('editorFileName');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads','');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            //echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo '/api/uploads/'.$info->getFilename(); 
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    public function uploadFile()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        if(empty($file)){
            echo 'no file';
            return;
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads','');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            //echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo '/api/uploads/'.$info->getFilename(); 
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }


}
