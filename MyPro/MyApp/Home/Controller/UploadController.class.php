<?php
namespace Home\Controller;
use Think\Controller;

/**
* 文件上传
*
* @author         zb
* @since          1.0
*/
class UploadController extends CheckController {

	//Uploadify上传
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

	/**
	 * 多文件上传
	 * @return [type] [description]
	 */
    public function index2(){
    	//var_dump($_FILES);exit;
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



	public function ExcelImport(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 31457280;
		$upload->rootPath =  C("CONST_UPLOADS");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');
		$upload->exts     = array('xlsx','xls');
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

	/**
	 * thinkphp上传图片
	 */
	public function uploadImg(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 512000;//50k
		$upload->rootPath =  C("CONST_UPLOADS_IMG");
		$upload->savePath = '';
		$upload->saveName = array('uniqid','');//为空则文件名不变,uniqid生成唯一字符串
		$upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
		$upload->autoSub  = true; //开启子目录
		$upload->subName  = array('date','Ymd'); // 子目录命名方式
		$info = $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$this->error($upload->getError());
		}else{// 上传成功
			/**
			 * 文件上传信息存储格式
			 * headPhotoFile为type="file"的name
			 * $info["headPhotoFile"]['name']:原文件名
			 * $info["headPhotoFile"]['type']:文件类型
			 * $info["headPhotoFile"]['size']:文件大小 BYTE
			 * $info["headPhotoFile"]['key']:input框ID
			 * $info["headPhotoFile"]['md5']:暂时不知道
			 * $info["headPhotoFile"]['sha1']:暂时不知道
			 * $info["headPhotoFile"]['savename']:文件保存名称
			 * $info["headPhotoFile"]['savepath']:文件保存路径
			 */

			//图片地址
			$filepath='Uploads/img/'.$info["headPhotoFile"]['savepath'].$info["headPhotoFile"]['savename'];

			/**
			 * 缩略图不启用
			 *
				//缩略图地址
				$filepath_thumb='Uploads/img_thumb/'.$info["headPhotoFile"]['savename'];

				//生成缩略图
				$image = new \Think\Image();
				$image->open($filepath);
				$image->thumb(150, 150)->save($filepath_thumb);

				*/

			//$filepath=str_replace('/', '', $filepath);
			header("location: ../Photos/photoCut?url=".$filepath);
		}

	}








}


