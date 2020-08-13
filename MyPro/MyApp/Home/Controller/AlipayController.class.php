<?php
namespace Home\Controller;
use Think\Controller;

class AlipayController extends CheckController {
    public function index(){ 
    	$this->display();
	}
	//支付宝付款
	public function pay(){
		header("Content-type:text/html;charset=utf-8");
		vendor('Alipay.AlipayNotify');
		vendor('Alipay.AlipaySubmit');
		/**************************请求参数**************************/
	
		//支付类型
		$payment_type = "1";
		//必填，不能修改
		//服务器异步通知页面路径
		
		//此页面为付款成功的展示页面
		$notify_url = "http://101.200.126.178/Member/notify_url";
		//需http://格式的完整路径，不能加?id=123这类自定义参数
	
		//页面跳转同步通知页面路径
		
		//此页面为付款成功的展示页面
		$return_url = "http://101.200.126.178/Member/return_url";
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
	
		//商户订单号
		$out_trade_no = I('WIDout_trade_no');
		//商户网站订单系统中唯一订单号，必填
	
		//订单名称
		$subject = I('WIDsubject');
		//必填
	
		//付款金额
		$price = I('WIDprice');
		//必填
	
		//商品数量
		$quantity = "1";
		//必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
		//物流费用
		$logistics_fee = "0.00";
		//必填，即运费
		//物流类型
		$logistics_type = "EXPRESS";
		//必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
		//物流支付方式
		$logistics_payment = "SELLER_PAY";
		//必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
		//订单描述
	
		$body = I('WIDbody');
		//商品展示地址
		$show_url = I('WIDshow_url');
		//需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html
	
		//收货人姓名
		$receive_name = I('WIDreceive_name');
		//如：张三
	
		//收货人地址
		$receive_address = I('WIDreceive_address');
		//如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
	
		//收货人邮编
		$receive_zip = I('WIDreceive_zip');
		//如：123456
	
		//收货人电话号码
		$receive_phone = I('WIDreceive_phone');
		//如：0571-88158090
	
		//收货人手机号码
		$receive_mobile = I('WIDreceive_mobile');
		//如：13312341234
	
	
		/************************************************************/
	
	  
	    
		/************************************************************/
	
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "trade_create_by_buyer",
				"partner" => trim(C('alipay_config')['partner']),
				"seller_email" => trim(C('alipay_config')['seller_email']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"price"	=> $price,
				"quantity"	=> $quantity,
				"logistics_fee"	=> $logistics_fee,
				"logistics_type"	=> $logistics_type,
				"logistics_payment"	=> $logistics_payment,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"receive_name"	=> $receive_name,
				"receive_address"	=> $receive_address,
				"receive_zip"	=> $receive_zip,
				"receive_phone"	=> $receive_phone,
				"receive_mobile"	=> $receive_mobile,
				"_input_charset"	=> trim(strtolower(C('alipay_config')['input_charset']))
		);
	
	
	
		 
		//建立请求
		$alipaySubmit = new \AlipaySubmit(C('alipay_config'));
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		 
		echo $html_text;
	}

	
	
	public function notify_url(){
		vendor('Alipay.AlipayNotify');
		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify(C('alipay_config'));
		$verify_result = $alipayNotify->verifyNotify();
	
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
				
	
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
	
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
			//商户订单号
				
			$out_trade_no = $_POST['out_trade_no'];
				
			//支付宝交易号
				
			$trade_no = $_POST['trade_no'];
				
			//交易状态
			$trade_status = $_POST['trade_status'];
				
				
			if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
				//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
	
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
	
				echo "success";		//请不要修改或删除
					
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
				//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
	
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
	
				echo "success";		//请不要修改或删除
					
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
				//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
	
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
	
				echo "success";		//请不要修改或删除
					
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//该判断表示买家已经确认收货，这笔交易完成
	
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
	
				echo "success";		//请不要修改或删除
					
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
			else {
				//其他状态判断
				echo "success";
					
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}
				
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			echo "fail";
				
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}
	public function return_url(){
		vendor('Alipay.AlipayNotify');
		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify(C('alipay_config'));
		$verify_result = $alipayNotify->verifyReturn();
	
		$flag='ok';//定义一个状态
		
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代码
	
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
	
			//商户订单号
	
			$out_trade_no = $_GET['out_trade_no'];
	
			//支付宝交易号
	
			$trade_no = $_GET['trade_no'];
	
			//交易状态
			$trade_status = $_GET['trade_status'];
	
			if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
			}
			else if($_GET['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
			}
			else {
				echo "trade_status=".$_GET['trade_status'];
			}
	
			//echo "验证成功<br />";
			//echo "trade_no=".$trade_no;
				
			$this->assign('trade_no',$trade_no);
				
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
				
			//加入自己的逻辑代码

			/**
			 *支付成功后往数据库更改订单状态为付款成功
			 */
			$userid=session('userid');
			$data['trade_no']=$trade_no; //支付宝订单id
			$data['paytype']=1; //已付款
				
			$result=M('userorder')->where('id="%s" and memberid="%d"',$out_trade_no,$userid)->save($data);
			if ($result!==false){
	
			}else {
				$flag=='error';
				$this->assign('info','支付宝付款成功但交易状态未更新，请联系商家！');
			}
			
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
			// echo "验证失败";
	
			$flag='error';
	
		}
	
		$this->assign('flag',$flag);
		$this->display();
	}	
	
	
	
	

}

 
