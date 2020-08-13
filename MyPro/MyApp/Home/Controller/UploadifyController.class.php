<?php
namespace Home\Controller;
use Think\Controller;
class UploadifyController extends CheckController {
    public function index(){
        $this->display();
    }
    
    //自己封的类的文件上传
    public function uploadimg(){
    	vendor('FileUpload');
    	$up=new \FileUpload(array('isRandName'=>true,'allowType'=>array('txt', 'png', 'jpg', 'gif'),'FilePath'=>'./Uploads/', 'MAXSIZE'=>2000000,'isThumb'=>true));
    	//$up->uploadFile('upload');
    	if($up->uploadFile('upload')){
    		echo $up->getNewFileName().'|'.$up->getOriginName();
    	}else{
    		echo $up->getErrorMsg();
    	}
    }
    
    
    public function index2(){
        var_dump($_FILES);
    	if (!empty($_FILES)){
	    	$upload = new \Think\Upload();// 实例化上传类
	    	$upload->maxSize = 512000;//50k
	    	$upload->rootPath = 'Uploads/files/';
	    	$upload->savePath = '';
	    	$upload->saveName = array('uniqid','');//为空则文件名不变,uniqid生成唯一字符串
	    	$upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
	    	$upload->autoSub  = true; //开启子目录
	    	$upload->subName  = array('date','Ymd'); // 子目录命名方式
	    	$info = $upload->upload();
	    	if(!$info) {// 上传错误提示错误信息
	    		$this->error($upload->getError());
	    	}else{// 上传成功
	    		//图片地址
	    		//var_dump($info);
	    		$filepath='Uploads/files/'.$info["file"]['savepath'].$info["file"]['savename'];
	    		$this->assign('filepath',$filepath);
	    	}
    	}
    	$this->display();
    }
    
    
}