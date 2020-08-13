<?php
//后台首页
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
	/**
	 * 用户管理页面
	 */
	public function index(){
		$this->display();
	}
	/**
	 * 是否展示添加用户按钮
	 */
	public function  isshow(){
		$userId = session('userId');
		$Mode_user = M('user');
		$data=$Mode_user->where("id='%d'",$userId)->field('id,username')->find();
		$this->ajaxReturn($data);
	}
	/**
	 * 显示所有用户
	 */
	public function showAll(){
		$userId = session('userId');
		$Mode_user = M('user');
		$data=$Mode_user->where("id='%d'",$userId)->field('ifadmin')->find();
		$Model = M();
		if($data['ifadmin']==1){
			$sql="SELECT m.id,m.username,if(m.ifuse=1,'可用','不可用') ifuse,if(m.ifadmin=1,'管理员','普通用户') ifadmin FROM t_user m ";
			$data=$Model->query($sql);
		}else{
			$sql="SELECT m.id,m.username,if(m.ifuse=1,'可用','不可用') ifuse,if(m.ifadmin=1,'管理员','普通用户') ifadmin FROM t_user m WHERE  m.id=".$userId;
			$data=$Model->query($sql);
		}
		$data=$Model->query($sql);
		$this->ajaxReturn($data);
	}
	/**
	 * 删除用户
	 */
	public function del(){
		$id = I('id/d',0);
		$Model_user = M('user');
		$data=$Model_user->delete($id);
		$this->ajaxReturn($data);
	}
	/**
	 * 重置密码
	 */
	public function resetPasswd(){
		$id = I('id/d',0);
		$Model_user = M('user');
		$data=$Model_user->where('id="%d"',$id)->setField('pwd','e10adc3949ba59abbe56e057f20f883e');
		$this->ajaxReturn($data);
	}
	/**
	 * 禁用用户
	 */
	public function userStatus(){
		$id = I('id/d',0);
		$ifuse = I('ifuse/s',0);
		$Model_user = M('user');
		if ($ifuse=='可用'){
			$ifuse=0;//如果当前用户状态是可用的，就是禁用
		}else {
			$ifuse=1;//如果当前用户状态是禁用的，就是启用
		}
		$Model_user->ifuse = $ifuse;
		$data=$Model_user->where('id="%d"',$id)->save();
		$this->ajaxReturn($data);
	}
	/**
	 * 用户添加页面
	 */
	public function useradd(){
		$this->display();
	}
	/**
	 * 数据库写入用户信息
	 */
	public function addUserInfo(){
		$usernameinfo = I('usernameinfo/s',0);
		$passwd = I("passwd/s",0);
		$passwdNext = I('passwdNext/s',0);
		$Model_user = M('user');
		$list=$Model_user->where('username="%s"',$usernameinfo)->find();
		if ($list==null){
			$data['username'] = $usernameinfo;
			$data['pwd'] = md5($passwd);
			$Model_user->add($data);
			echo 'ok';
		} else{
			echo 'error';
		}
	}
	/**
	 * 用户信息修改页面
	 */
	public function userupdate(){
		$id = I('id/d',0);
		$this->assign('id',$id);
		$this->display();
	}
	/**
	 * 修改用户信息
	 */
	public function editUserInfo(){
		$id = I('id/d',0);
		$courseId = I('courseId/d',0);
		$oldPasswd = I('oldPasswd/s',0);
		$newPasswd = I('newPasswd/s',0);
		$Model_user = M('user');
		$data=$Model_user->field('pwd')->where('id="%d"',$id)->find();
		if ($data['pwd']==md5($oldPasswd)){
			$userinfo['pwd']=md5($newPasswd);
			$status=$Model_user->where('id="%d"',$id)->save($userinfo);
			echo 'ok';
		}else{
			echo 'error';
		}
	}

	//用户注册页面
	public function regist(){
		$this->display();
	}



	//用户登录界面

	public function loginpage(){
		$this->display();
	}




	public function getinfo(){
		$m = M('words');
		$data = $m->limit(5)->select();
		$this->ajaxReturn($data);
	}

	public function delById(){
		$id = I('id/d',0);
		M('words')->delete($id);
		echo 'ok';
	}

	public function postfun(){
		$json = file_get_contents("php://input");
		$content = json_decode($json,true);
var_dump($content['name']);
exit;
		$id = I('id/d',0);
		$name = I('name/s','');
		echo $id.':'.$name;
	}




}