<?php
namespace Home\Controller;
use Think\Controller;

class FunController extends CheckController {
    public function index(){
    	$this->display();
	}

	/**
	 * 发送邮件
	 */
	public function mail(){
		vendor('PHPMail.PHPMailer');
		$title=I('title/s');
		$content=I('content/s');
		$address=I('address/s');
		if(SendMail($address,$title,$content)){
			echo'发送成功！';
		}
		else{
			echo'发送失败';
		}
	}






}


