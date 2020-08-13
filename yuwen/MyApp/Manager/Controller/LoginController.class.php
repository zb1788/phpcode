<?php
namespace Manager\Controller;
use Think\Controller;
/**/
class LoginController extends Controller {
    public function index(){
        $this->display();
    }
    
     /**
     * 登录
     */
    public function login(){
//     	echo 'aaa';
    	$username = I('username/s');
        $pwd = I('pwd');
        $pwd = md5($pwd);
        //echo $pwd;
        $admin=M("admin");
        $rs=$admin -> where("username='%s'",$username) -> field("id,username,pwd,ifadmin,ifuse") -> find();
        //$this ->ajaxReturn($rs);
        //dump($rs);
        if(count($rs)==1)
        {
            $arr_return['flag']=0;
            $arr_return['errorinfo']="账号不存在，请重新输入";
        }
        else{
            if ($pwd == $rs['pwd']) {
            	session('adminuser',$username);
    		    session('adminuserid',$rs['id']);
    		    session('ifadmin',$rs['ifadmin']);
                 // $_SESSION["adminuser"] = $username;
                 // $_SESSION["adminuserid"] = $rs['id'];
                 // $_SESSION["ifadmin"] = $rs['ifadmin'];
                 if ($rs['ifuse'] == 0) {
                 	$arr_return['flag']=0;
                 	$arr_return['errorinfo']="该账号未启用，请联系管理员";
                 }
                 else
                {
                    $arr_return['flag']=1;
                 	$arr_return['errorinfo']="";
                }

            }
            else{
                $arr_return['flag']=0;
                $arr_return['errorinfo']="密码错误，请重新输入";
            }
        }
    	$this -> ajaxReturn($arr_return);
    }
    /**
     * 退出登录
     */
    public function logout(){
    		session('[destroy]');
    		//$this->redirect('../login/index');
    	 
    }
}