<?php
/**
 * 用户登录session验证
 * @author Zhangbo1
 *
 */
namespace Home\Controller;
use Think\Controller;
class CheckController extends Controller {

    public function _initialize(){
        if (empty(session('userName'))){
            $this->redirect('login/index');
        }
    }
}