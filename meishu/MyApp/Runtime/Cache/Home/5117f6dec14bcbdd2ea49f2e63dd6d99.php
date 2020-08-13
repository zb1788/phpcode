<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>菜单</title>
<script language="JavaScript" src="/Public/Home/js/jquery.min.js"></script>
<link href="/Public/Home/style/menu.css" rel="stylesheet" type="text/css" />
<script  type="text/javascript">
$(function(){
	//导航切换
	$(".menuson li").click(function(){
		$(".menuson li.active").removeClass("active")
		$(this).addClass("active");
	});

	$('.title').click(function(){
		var $ul = $(this).next('ul');
		$('dd').find('ul').slideUp();
		if($ul.is(':visible')){
			$(this).next('ul').slideUp();
		}else{
			$(this).next('ul').slideDown();
		}
	});
})
</script>
</head>
<body>
    <div class="lefttop"><span></span>功能菜单</div>
    <dl class="leftmenu">
        <dd>
            <div class="title"><span></span>美术信息管理</div>
            <ul class="menuson">
                <li><cite></cite><a href="<?php echo U('Art/kecheng');?>" target="mainFrame">课程信息管理</a><i></i></li>
                <li><cite></cite><a href="<?php echo U('Art/music');?>" target="mainFrame">背景音乐管理</a><i></i></li>
            </ul>
        </dd>
    </dl>
</body>
</html>