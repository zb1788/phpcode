<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
<style> 
	html, body{ margin:0; padding:0;}
	body{background:url(/Public/Home/images/topbg.gif) 0 0 repeat-x; color:#FFF; line-height:1.5; font-size:12px;}
	a{color:#FFF; font-size:12px;text-decoration:none;}
</style>
<script language="JavaScript" src="/Public/Home/js/jquery.min.js"></script>
<script type="text/javascript">
function toback(){
  $.get('../login/logout',function(){
    parent.window.location.href="../login/index";
  });
  }
</script>
</head>
<body> 
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="50%" height="74" valign="bottom"><img src="/Public/Home/images/loginlogo.png" width="280" height="70" /></td>
    <td width="50%" align="right" valign="top" style="padding:10px 10px 0 0;"> 
	    欢迎你&nbsp;<span id="name"><?php echo (session('adminuser')); ?></span>     
      <a href="#">帮助</a> |
      <a href="#">关于</a> | 
	    <a href="javascript:toback();">退出</a>
    </td>
  </tr>
</table>

</body>
</html>