<?php
namespace Manager\Controller;
use Think\Controller;

class UploadController extends CheckController {

    public function uploadfile(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728000;
        $upload->rootPath =  C("CONST_UPLOADS");
        $upload->savePath = '';
        $upload->saveName = array('uniqid','');
        $upload->exts     = array('jpg','jpeg','png','gif','bmp');
        $upload->autoSub  = true;
        $upload->subName  =  array('date','Ymd'); // array('date','Ym');
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $arr_return["issuc"] = 0;
            $arr_return["msg"] = $upload->getError();
        }else{// 上传成功
            // $arr_return["issuc"] = 1;
            // $arr_return["msg"] = $info["file"];
            $filepath='upload/pic/'.$info["file"]['savepath'].$info["file"]['savename'];
            
            $arr_return["issuc"] = 1;
			$arr_return["msg"] = base64EncodeImage($filepath);
        }
        $this->ajaxReturn($arr_return);
    }



}


