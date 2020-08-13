<?php
namespace Manager\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
    	$this->display();
    }

    /**
     * 登录
     */
    public function login(){
    	$userName=I('username/s','');
    	$passwd=I('pwd/s','');
    	$userName=trim($userName);
    	$passwd=md5(trim($passwd));
    	$Dao=M('user_admin');
    	$data=$Dao->where("username='%s'",$userName)->field('id,username,pwd,ifadmin,truename')->find();
        if(empty($data)){
		   $info['status'] = 'error';
		   $info['msg'] = '用户名或者密码错误';
        }else{
			$dbpasswd=$data['pwd'];
			$userId=$data['id'];
			$ifadmin=$data['ifadmin'];
			$truename = $data['truename'];
	
			if ($passwd==$dbpasswd){
				session('userId',$userId);
				session('userName',$userName);
				session('trueName',$truename);
				session('ifadmin',$ifadmin);
				$info['status'] = 'ok';
		   		$info['msg'] = '登录成功';
			}else {
				$info['status'] = 'error';
		   		$info['msg'] = '用户名或者密码错误';
			}
		}
		$this->ajaxReturn($info);
    }


    /**
     * 退出登录
     */
    public function logout(){
    		session('[destroy]');
    		$this->redirect('login/index');
    }
}