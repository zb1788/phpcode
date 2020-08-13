<?php
class ValidateCode{
	private $num=4;	//验证码的长度
	private $type=0; //验证码的类型：0：纯数字 1：数字+小写字母  2：数字+大小写字符
	private $height=30;//高度
	private $width;//宽度
	private $im; //画布
	private $str =  array();
	private $color;

	//构造方法初始化
	public function __construct($num,$type) {
		$this->font = dirname(__FILE__).'/public/msyh.ttf';//注意字体路径要写对，否则显示不了图片
		$this->num=$num;
		$this->type=$type;
	}

	//生成背景
	private function createBg(){
		$this->width=$this->num*20;
		$this->im = imagecreatetruecolor($this->width,$this->height);//创建一个画布
		//定义几个颜色（输出不同颜色的验证码）
		$color[] = imagecolorallocate($this->im,111,0,55);
		$color[] = imagecolorallocate($this->im,0,77,0);
		$color[] = imagecolorallocate($this->im,0,0,160);
		$color[] = imagecolorallocate($this->im,221,111,0);
		$color[] = imagecolorallocate($this->im,220,0,0);
		$bg = imagecolorallocate($this->im,240,240,240);//背景
		//2. 开始绘画
		imagefill($this->im,0,0,$bg);
		imagerectangle($this->im,0,0,$this->width-1,$this->height-1,$color[rand(0,4)]);
	}

	//增加干扰
	private function createLine(){
		//随机添加干扰点
		for($i=0;$i<200;$i++){
			$c = imagecolorallocate($this->im,rand(0,255),rand(0,255),rand(0,255));//随机一个颜色
			imagesetpixel($this->im,rand(0,$this->width),rand(0,$this->height),$c);
		}

		//随机添加干扰线
		for($i=0;$i<5;$i++){
			$c = imagecolorallocate($this->im,rand(0,255),rand(0,255),rand(0,255));//随机一个颜色
			imageline($this->im,rand(0,$this->width),rand(0,$this->height),rand(0,$this->width),rand(0,$this->height),$c);
		}
	}

	//生成验证码
	private function createFont(){
		//绘制验证码内容（一个一个字符绘制）：
		$this->str=$this->getCode();
		$color[] = imagecolorallocate($this->im,111,0,55);
		$color[] = imagecolorallocate($this->im,0,77,0);
		$color[] = imagecolorallocate($this->im,0,0,160);
		$color[] = imagecolorallocate($this->im,221,111,0);
		$color[] = imagecolorallocate($this->im,220,0,0);
		for($i=0;$i<$this->num;$i++){
			imagettftext($this->im,18,rand(-40,40),8+(18*$i),24,$color[rand(0,4)],"public/msyh.ttf",$this->str[$i]);
		}
	}
	//输出图像
	private function createImg(){
		//3. 输出图像
		header("Content-Type:image/png");//设置响应头信息(注意此函数实行前不可以有输出)
		imagepng($this->im);
		//4. 销毁图片（释放内容）
		imagedestroy($this->im);
	}
	//对外生成
	public function doImg(){
		$this->createBg();
		$this->createLine();
		$this->createFont();
		$this->createImg();
	}
	//获取验证码
	public function getCodeInfo(){
		return $this->str;
	}
    /**
     * 随机生成一个验证码的内容的函数
     * @param $m :验证码的个数（默认为4）
     * @param $type : 验证码的类型：0：纯数字 1：数字+小写字母  2：数字+大小写字符
     */
    private function getCode(){
    	$m=$this->num;
    	$type=$this->type;
    	$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	$t = array(9,35,strlen($str)-1);
    	//随机生成验证码所需内容
    	$c="";
    	for($i=0;$i<$m;$i++){
    		$c.=$str[rand(0,$t[$type])];
    	}
    	return $c;
    }
}