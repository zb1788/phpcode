<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>课程列表</title>
<style>
 .page{width:auto;height:25px; margin:auto; line-height:25px;}
 .page a{display:block; height:25px; padding:0px 6px; border:solid 1px #e7e7e7; border-radius:3px; color:#333; font-family:'微软雅黑'; font-size:13px; text-align:center; text-decoration:none;float:left; margin-right:10px;min-width:20px;}
 .page a:hover, .page a.this{background:#f7f7f7; font-weight:bold}
</style>
<link href="/Public/Home/style/manage.css" rel="stylesheet" type="text/css" />
<link href="/Public/Home/js/uploadify/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/Home/js/jquery.min.js"></script>
<script type="text/javascript" src="/Public/Home/js/common.js"></script>
<script type="text/javascript" src="/Public/Home/js/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="/Public/Home/js/iframeTools.js"></script>
<script type="text/javascript" src="/Public/Home/js/uploadify/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="/Public/Home/js/jPlayer/dist/jplayer/jquery.jplayer.min.js"></script>
</head>
<body>
<div class="place"><strong>位置</strong>：首页 &gt; 课程管理</div>
<div class="container">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="box_border">
	  <tr>
	    <td class="box_top pl_10 f14"><strong>课程管理</strong></td>
	  </tr>
	  <tr>
	    <td class="pl_5">
	    	<table border="0" cellspacing="0" cellpadding="0" class="form_table">
		      <tr>
		        <td align="left">年级：</td>
		        <td>
		          <select id="grade" class="select">
		          <option value="">全部</option>
				  	<option value="一年级">一年级</option>
				  	<option value="二年级">二年级</option>
				  	<option value="三年级">三年级</option>
				  	<option value="四年级">四年级</option>
				  	<option value="五年级">五年级</option>
				  	<option value="六年级">六年级</option>
		          </select>
		        </td>
				<td  align="left">学期：</td>
				<td>
		          <select id="term" class="select">
		          <option value="">全部</option>
				  	<option value="上学期">上学期</option>
				  	<option value="下学期">下学期</option>
		          </select>
		        </td>
				<td align="left">版本：</td>
				<td>
					<!-- <input type="text" id="version" class="input-text" size="10"/> -->
		          <select name="version" id="version" class="select">
		          </select>
				</td>
				<td align="left">课程：</td>
				<td>
					 <input type="text" id="kecheng" class="input-text" size="10"/>
					 <!--
			          <select name="kecheng" id="kecheng" class="select">
			          </select>
			          -->
				</td>
				<td>&nbsp;&nbsp;<input type="button" id="chaxun" value="查询" class="btn btn82 btn_search"/></td>
		      </tr>
	    	</table>
		</td>
	  </tr>
	</table>
	<div class="h5"></div>
	<table border="0" cellspacing="0" cellpadding="0" class="form_table">
		<tr>
			<td>
				<input type="button" id="addKecheng" value="添加课程" class="ext_btn ext_btn_submit" />
			</td>
		</tr>
	</table>
	<div class="h10"></div>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list_table" id="table_data">
	  <tr>
	    <th>年级</th>
	    <th>学期</th>
	    <th>版本</th>
	    <th>课程</th>
	    <th>短名称</th>
	    <th>模版</th>
	    <th>操作</th>
	  </tr>
	</table>
	<div class="h5"></div>
	<div class="page"></div>
</div>


<table style="display:none;" id="demo">
	<tr class="tr">
		<td>年级</td>
		<td>学期</td>
		<td>版本</td>
		<td align="left">课程</td>
		<td align="left">课程</td>
		<td></td>
		<td align="left">
			<input type="button" name="edit" value="编辑" class="ext_btn ext_btn_success" />
			<input type="button" name="manage" value="内容管理" class="ext_btn ext_btn_listen" />
			<!--
			<input type="button" name="copy" value="复制" class="ext_btn ext_btn_success" />
			-->
			<input name="makeThumb" type="button" class="ext_btn ext_btn_success" value="生成缩略图" />
			<input type="button" name="down" value="下载" class="ext_btn ext_btn_error" />
			<input type="button" name="del" value="删除" class="ext_btn ext_btn_error" />
		</td>
	</tr>
</table>
<div id="jplayer"></div>
</body>
</html>
<script type="text/javascript">
var pagesize=10;
$(function(){
  $.ajaxSetup({async:false});
  $('#chaxun').click(function(){
  	pagelist(1,pagesize);
  });

  $("#grade").change(function(){
    getBanben();
  });
  $("#term").change(function(){
    getBanben();
  });
  $("#version").change(function(){
    //getKecheng();
  });



});





function getBanben()
{
  $("#version").empty();
  var nianji = $("#grade").val();
  var xueqi = $("#term").val();
  $.getJSON("../Art/kcbanben", {nianji:nianji,xueqi:xueqi,random:Math.random()}, function(data){
    $("#kecheng").empty();
    $.each(data, function(i,val){
      $('#version').append($("<option>").val(val.detail_name).text(val.detail_name));
    });
  });
  //getKecheng();
}

function getKecheng()
{
  $("#kecheng").empty();
  var nianji = $("#grade").val();
  var xueqi = $("#term").val();
  var versionid = $("#version").val();
  $.getJSON("../Art/kckecheng", {nianji:nianji,xueqi:xueqi,versionid:versionid,random:Math.random()}, function(data){
    $.each(data, function(i,val){
      $('#kecheng').append($("<option>").val(val.id).text(val.ks_name));
    });
  });
}



//添加课程
$('#addKecheng').click(function(){
	var myDialog = $.dialog.open('addkecheng',{
		title:'添加课程',
		window : 'top',
		width : 340,
		height : 390,
		lock : true,
		opacity : 0.3,
		button : [
				 {
				 	name:'保存',
					callback:function(){
						var iframe = this.iframe.contentWindow;
						var re = iframe.savedata();//调用窗口的方法
						if(re==false){
							art.dialog.alert('添加失败！');
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

//删除课程
$('input[name="del"]').live('click',function(){
	var id=$(this).attr('bid');//课程id
	var aa=$(this).parent().parent();
	if($.dialog.confirm('确定删除？',function(){
		$.get('../Art/delkecheng',{ran:Math.random(),id:id},function(data){
			$(aa).remove();
		});
	}));
});
//下载课程
$('input[name="down"]').live('click',function(){
	var id=$(this).attr('bid');//课程id
	window.open('../Art/flashdown?ran='+Math.random()+'&kechengid='+id);
//	$.get('../Kewen/flashdown',{ran:Math.random(),kechengid:id},function(data){
//
//	});
});

//复制课程信息
$('input[name="copy"]').live('click',function(){
	var id=$(this).attr('bid');//课程id
	$.get('../Art/copyKecheng',{ran:Math.random(),id:id},function(data){
		pagelist(1,pagesize);
	});
});
//编辑课程信息
$('input[name="edit"]').live('click',function(){
	var id=$(this).attr('bid');//课程id
	var aa=$(this).parent().parent();
	var myDialog = $.dialog.open('editkecheng?id='+id,{
		title:'添加课程',
		window : 'top',
		width : 340,
		height : 390,
		lock : true,
		opacity : 0.3,
		button : [
				 {
				 	name:'保存',
					callback:function(){
						var iframe = this.iframe.contentWindow;
						var re = iframe.savedata();//调用窗口的方法
						if(re==false){
							art.dialog.alert('添加失败！');
						}
						var dd=art.dialog.data('kechenginfos');
						var arr=dd.split('|');
						$(aa).children('td').eq(0).html(arr[0]);
						$(aa).children('td').eq(1).html(arr[1]);
						$(aa).children('td').eq(2).html(arr[2]);
						$(aa).children('td').eq(3).html(arr[3]);
						$(aa).children('td').eq(4).html(arr[4]);
						$(aa).children('td').eq(5).html(mobanleixing(arr[5]));
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
//内容管理
$('input[name="manage"]').live('click',function(){
	var id=$(this).attr('bid');//课程id
	location.href='kechenginfo?id='+id;
});

//生成缩略图
$('input[name="makeThumb"]').live('click',function(){
	var id = $(this).attr('bid');//课程id
	$.get('../Art/makexml',{ran:Math.random(),id:id},function(data){
		if(data.qt=='ok'&&data.xxhz=='ok'){
			window.open("online.html?xml="+data.path+'&kid='+id);
		}else{
			if(data.qt!='ok'){
				art.dialog.alert(data.qt);
				return false;
			}
			if(data.xxhz!='ok'){
				art.dialog.alert(data.xxhz);
				return false;
			}
		}
	});
});



//分页查询
 function pagelist(pageCurrent,page_size){
 	var grade=$('#grade').val();
	var term=$('#term').val();
	var version=$('#version').val();
	var kecheng=$('#kecheng').val();
 	$.get("../Art/fenye",
 			{
 			  ran:Math.random(),
 			  pageCurrent:pageCurrent,
 			  page_size:page_size,
 			  grade:grade,
			  term:term,
			  version:version,
			  kecheng:kecheng
 			},
 			function(data){
			$(".page").empty();
			$('.list_table tr:not(:first)').remove();
			$.each(data,function(k,v){
				$('.page').html(k);
				$.each(v,function(k,v){
					//alert(v.nianji);
					var tr=$('#demo').children('tbody').children('tr').eq(0).clone();
					tr.children('td').eq(0).html(v.nianji);
					tr.children('td').eq(1).html(v.xueqi);
					tr.children('td').eq(2).html(v.banben);
					tr.children('td').eq(3).html(v.kecheng);
					tr.children('td').eq(4).html(v.title);
					tr.children('td').eq(5).html(mobanleixing(v.mobanid));
					tr.find('input').attr('bid',v.id);
					tr.find('input').attr('dvideo',v.filepath);
					tr.appendTo('.list_table');
				});
			});
 			$("#SelectPages").change(function(){
 				 pagelist($("#SelectPages").val(),page_size);
 				 });
 		})
 }
 //模版类型
 function mobanleixing(mobanid){
	if(mobanid==1){
		moban='花朵模版无拓展';
	}else if(mobanid==2){
		moban='花朵模版有拓展';
	}else if(mobanid==3){
		moban='画布模版无拓展';
	}else if(mobanid==4){
		moban='画布模版有拓展';
	}
	return moban;
 }






</script>