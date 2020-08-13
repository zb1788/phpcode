<?php
/**
 * 用户登录
 * @author Zhangbo1
 *
 */
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
        $this->display();
    }

    /**
     * 登录
     */
    public function login(){
        $userName=I('userName/s',0);
        $passwd=I('passwd/s',0);
        $userName=trim($userName);
        $passwd=md5(trim($passwd));


        $Dao=M('user');
        $data=$Dao->where("username='%s' and ifuse=1",$userName)->field('id,username,pwd')->select();

        if(empty($data)){
            echo '用户名不对';//用户名不对
            exit;
        }

        $dbpasswd=$data[0]['pwd'];
        $userId=$data[0]['id'];
        if ($passwd==$dbpasswd){
            session('userId',$userId);
            session('userName',$userName);
            echo 'ok';
        }else {
            echo '密码不对';//密码不对
        }
    }

    /**
     * 退出登录
     */
    public function logout(){
            session('[destroy]');
            $this->redirect('login/index');
    }
}