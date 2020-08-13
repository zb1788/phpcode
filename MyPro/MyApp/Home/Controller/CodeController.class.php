<?php
namespace Home\Controller;
use Think\Controller;
class CodeController extends Controller {
    public function form(){
        $this->display();
    }

    /**
     * thinkphp的验证码
     * @return [type] [description]
     */
    public function tpcode(){
        $Verify = new \Think\Verify();
        // 验证码字体使用 ThinkPHP/Library/Think/Verify/ttfs/5.ttf
        $Verify->fontttf = '5.ttf';
        $Verify->entry();
    }


    //调用验证码类  回头整理
    public function code(){
        //自己写的验证码
    	vendor('ValidateCode');
    	$vc=new \ValidateCode(4,2);
    	$vc->doImg();
    	$code= $vc->getCodeInfo();
        //echo $code;
    	session('vcode',$code);
    }
    public function check(){
    	//如果输入的验证码和session中的一样就是正确
    	//echo session('code');
        $vcode = I('code/s');

        if($vcode != session('vcode')){
            echo '验证码错误';//验证码错误
            exit;
        }
    }
}