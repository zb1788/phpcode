<?php
namespace Home\Controller;
use Think\Controller;

/**
* 文件上传
*
* @author         gm
* @since          1.0
*/
class UploadController extends Controller {
    public function index(){
   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 31457280;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		$upload->exts     = array('mp3','wmv');
		$upload->autoSub  = true;
		$upload->subName  = array('date','Ymd'); // array('date','Ym');
	    $info = $upload->upload();

	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
	    	$arr_return["msg"] = $upload->getError();
	    	$this->ajaxReturn("");
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
	    	$arr_return["msg"] = $info["Filedata"];
	    	$this->ajaxReturn($arr_return);
	    }
	}

    public function uploadpics(){


   		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728000000;
		$upload->rootPath =  './'.$_GET['path'];
		$upload->savePath = '';
		$upload->saveName = '';
		$upload->replace = true;
		$upload->exts     = array('jpg','jpeg');
		$upload->autoSub  = false;
	    $info = $upload->upload();

	    if(!$info) {// 上传错误提示错误信息
	    	$arr_return["issuc"] = 0;
	    	$arr_return["msg"] = $upload->getError();
	    	$this->ajaxReturn("");
	    }else{// 上传成功
	    	$arr_return["issuc"] = 1;
	    	$arr_return["msg"] = $info["Filedata"];
	    	$this->ajaxReturn($arr_return);
	    }
	}






}


