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
		$upload->maxSize = 3145728;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		$upload->exts     = array('xls', 'xlsx','jpg', 'gif', 'png', 'swf', 'mp3','wav');
		$upload->autoSub  = true;
		$upload->subName  = ""; // array('date','Ym');
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
	public function uploadfiles(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 31457280;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		$upload->exts     = array('mp3');
		$upload->autoSub  = true;
		$upload->subName  =  array('date','Ymd'); // array('date','Ym');
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

	public function uploadpic(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 31457280;
		$upload->rootPath =  C("CONST_PICUPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		$upload->exts     = array('jpg','png');
		$upload->autoSub  = true;
		$upload->subName  =  array('date','Ymd'); // array('date','Ym');
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
	/**
	 * 上传flash
	 */
	public function uploadFlash(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 31457280;
		$upload->rootPath =  C("CONST_FLASHUPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		$upload->exts     = array('swf');
		$upload->autoSub  = true;
		$upload->subName  =  array('date','Ymd'); // array('date','Ym');
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


