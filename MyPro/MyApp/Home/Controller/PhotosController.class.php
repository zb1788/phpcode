<?php
namespace Home\Controller;
use Think\Controller;

/** 
* 文件上传
*  
* @author         zb 
* @since          1.0 
*/  
class PhotosController extends CheckController {
	
	public function photo(){
		$this->display();
	}
	
	public function photoInfo(){
		$this->display();
	}
	
	//头像截取页面
	public function photoCut(){
		$url=I('url/s');
		$this->assign('url',$url);
		$this->display();
	}
	
	//截取头像方法
	public function cutPic(){
		$x=I('x');
		$y=I('y');
		$width=I('width');
		$height=I('height');
		$imgurl=I('imgurl');
		
		$imgurl=str_replace('../../', '', $imgurl);
		
		$ext = pathinfo($imgurl)['extension'];
 		$src_img='Uploads/img/'.date('Ymd').'/'.getUniName().'.'.$ext;
 		
		$image = new \Think\Image();
		$image->open($imgurl);
		$image->crop($width, $height, $x, $y);
		$image->save($src_img);
		
		unlink($imgurl);
		echo $src_img;
	}
	

}

 
