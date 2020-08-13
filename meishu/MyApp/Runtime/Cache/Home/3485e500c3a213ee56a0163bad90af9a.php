<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>版本列表</title>
<link href="/Public/Home/style/manage.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/Home/js/jquery.min.js"></script>
<script type="text/javascript" src="/Public/Home/js/common.js"></script>
<script type="text/javascript" src="/Public/Home/js/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="/Public/Home/js/iframeTools.js"></script>
</head>
<body>
<div class="place"><strong>位置</strong>：首页 &gt; 栏目管理</div>
<div class="container">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="box_border">
  <tr>
    <td class="box_top pl_10 f14"><strong>栏目管理</strong></td>
  </tr>
</table>
<div class="h5"></div>
<table border="0" cellspacing="0" cellpadding="0" class="form_table" width="100%">
	<tr>
		<td width="97%">
			<input type="button" id="addMusic" value="添加栏目" class="ext_btn ext_btn_submit" />
			<input type="hidden" id="bid" value="<?php echo ($bid); ?>" />
		</td>
		<td><a href="javascript:history.go(-1);" style="color: #333;font-weight:bold;font-size: 12px;text-decoration: none;">返回</a></td>
	</tr>
</table>
<div class="h10"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list_table" id="table_data">
  <tr>
  	<th>序号</th>
  	<th>栏目名称</th>
  	<th>栏目简介</th>
    <th>操作</th>
  </tr>
</table>
<div class="h5"></div>
		<table id="sorttable" width="100%" border="0" cellspacing="0" cellpadding="0" class="box_border" style="display:none;">
		  <tr>
		    <td class="pl_10" height="42">
		    	<input id="sort" type="button" class="ext_btn ext_btn_submit" value="保存次序">
			</td>
		  </tr>
		</table>
<div class="h5"></div>

</div>



<table style="display:none" id="demo">
	<tr>
		<td><input type="text" name="sort" style="width:30px;" value="" class="input-text"/></td>
		<td>音乐名称</td>
		<td>音乐名称</td>
		<td>
			<input type="button" name="edit"  value="编辑" class="ext_btn ext_btn_error"/>
<!-- 			<input type="button" name="thumb"  value="栏目缩略图" class="ext_btn ext_btn_success"/> -->
			<input type="button" name="addpic"  value="栏目图片" class="ext_btn ext_btn_submit"/>
			<input type="button" name="del"  value="删除" class="ext_btn ext_btn_error"/>
		</td>
	</tr>
</table>

</body>
</html>
<script type="text/javascript">
$(function(){
  $.ajaxSetup({async:false});
  var id = $('#bid').val();//课程id
  $.get('../Art/getColumns',{ran:Math.random(),id:id},function(data){
  	$.each(data,function(k,v){
  		var tr = $('#demo').children('tbody').children('tr').clone();
  		tr.children('td').eq(0).children('input').val(k+1);
  		tr.children('td').eq(1).html(v.name);
  		tr.children('td').eq(2).html(v.remark);
  		tr.find('input').attr('bid',v.id);
  		tr.appendTo('.list_table');
  	});
  	if(data!=''){
  		$('#sorttable').show();
  	}
  });
});



//图片管理
$('input[name="addpic"]').live('click',function(){
	var id=$(this).attr('bid');//课程id
	location.href='piclist?type=addpic&id='+id;
});



//添加栏目
$('#addMusic').click(function(){
	var id = $('#bid').val();//课程id
	var myDialog = $.dialog.open('addLanmu?type=add&id='+id,{
		title:'添加栏目',
		window : 'top',
		width : 540,
		height : 160,
		lock : true,
		opacity : 0.3,
		button : [
				 {
				 	name:'保存',
					callback:function(){
						var iframe = this.iframe.contentWindow;
						var re = iframe.savedata();//调用窗口的方法
						if(re){
							location.reload();
						}
						return re;
					},
					focus:true
				 },
				 {
					name : '关闭',
					callback : function() {
						return true;
					},
					focus : false
				} ]
	});
});


//删除栏目
$('input[name="del"]').live('click',function(){
	var id=$(this).attr('bid');//音频id
	var aa=$(this).parent().parent();
	$.get('../Art/delColumns',{ran:Math.random(),id:id},function(data){
		$(aa).remove();
	});
});
//编辑栏目
$('input[name="edit"]').live('click',function(){
	var cid=$(this).attr('bid');//栏目id
		var myDialog = $.dialog.open('addLanmu?type=edit&cid='+cid,{
		title:'编辑栏目',
		window : 'top',
		width : 540,
		height : 150,
		lock : true,
		opacity : 0.3,
		button : [
				 {
				 	name:'保存',
					callback:function(){
						var iframe = this.iframe.contentWindow;
						var re = iframe.savedata();//调用窗口的方法
						location.reload();
						return re;
					},
					focus:true
				 },
				 {
					name : '关闭',
					callback : function() {
						return true;
					},
					focus : false
				} ]
	});
});

//点击更新排序按钮
$('#sort').click(function(){
	var sortsInfoArray=[];
	$('.list_table input[name="sort"]').each(function(k,v){
		var obj={};
		obj.id=$(v).attr('bid');
		obj.sortid=$(v).val();
		sortsInfoArray.push(obj);
	});
	//alert(JSON.stringify(sortsInfoArray));return false;
	$.get('../Art/updateSort',{ran:Math.random(),sortsInfo:JSON.stringify(sortsInfoArray)},function(data){
		location.reload();
	});
});

</script>