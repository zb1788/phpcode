<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>添加课程</title>
<link href="/Public/Home/style/manage.css" rel="stylesheet" type="text/css" />
<link href="/Public/Home/js/uploadify/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/Home/js/jquery.min.js"></script>
<script type="text/javascript" src="/Public/Home/js/common.js"></script>
<script type="text/javascript" src="/Public/Home/js/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="/Public/Home/js/iframeTools.js"></script>
<script type="text/javascript" src="/Public/Home/js/uploadify/jquery.uploadify.min.js"></script>
</head>
<body>
<div class="container">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form_table">
		<tr>
	        <td align="right">年级：</td>
	        <td colspan="2">
	          <select id="grade" class="select">
			  	<option value="一年级">一年级</option>
			  	<option value="二年级">二年级</option>
			  	<option value="三年级">三年级</option>
			  	<option value="四年级">四年级</option>
			  	<option value="五年级">五年级</option>
			  	<option value="六年级">六年级</option>
	          </select>
	        </td>
		</tr>
		<tr>
			<td  align="right">学期：</td>
			<td colspan="2">
	          <select id="term" class="select">
			  	<option value="上学期">上学期</option>
			  	<option value="下学期">下学期</option>
	          </select>
	        </td>
		</tr>
		<tr>
			<td align="right">版本：</td>
			<td colspan="2">
				<!--
				<input type="text" id="version" class="input-text"/>
				-->
				<select name="version" id="version" class="select">
		        </select>
			</td>
		</tr>
		<tr>
			<td align="right">课程：</td>
			<td colspan="2">
				<!--
				<input type="text" id="kecheng" class="input-text" />
				-->
				<select name="kecheng" id="kecheng" class="select">
		        </select>
			</td>
		</tr>
		<tr>
			<td align="right">课程别名：</td>
			<td colspan="2">
				<input type="text" id="unit" class="input-text" />
			</td>
		</tr>
		<tr>
			<td align="right">背景音乐：</td>
			<td width="75px">
			  <select id="music" class="select">
			  	<?php if(is_array($music_group)): $i = 0; $__LIST__ = $music_group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	          </select>
			</td>
			<td>
	          <select id="music_file" class="select">
	          </select>
	        </td>
		</tr>
		<tr>
			<td align="right">动画模版：</td>
			<td colspan="2">
	          <select id="moban" class="select">
	          	<option value="1">花朵模版无拓展</option>
	          	<option value="2">花朵模版有拓展</option>
	          	<option value="3">画布模版无拓展</option>
	          	<option value="4">画布模版有拓展</option>
	          </select>
			</td>
		</tr>
		<!--
		<tr id="leixing">
			<td align="right">类型：</td>
			<td colspan="2">
	          <select id="types" class="select">
	          	<option value="1">现代文</option>
	          	<option value="2">现代诗</option>
				<option value="3">文言文</option>
	          	<option value="4">古诗</option>
	          </select>
			</td>
		</tr>
		<tr>
			<td align="right">示范朗读：</td>
			<td colspan="2">
				<input type="file" id="file_upload"  multiple="false" >
				<input type="hidden" name="filepath" id="filepath" value="" />
				<input type="hidden" name="filename" id="filename" value="" />
				<input type="hidden" name="issuc" id="issuc" value="" />
			</td>
		</tr>
		-->
</table>
<div class="h5"></div>



</body>
</html>
<script type="text/javascript">
$.ajaxSetup({async:false});
$(function(){
	var music_group_id=$('#music').val();
	$.get('../Art/queryMusic',{ran:Math.random(),music_group_id:music_group_id},function(data){
		$.each(data,function(k,v){
			var aa='<option value="'+v.id+'">'+v.music_name+'</option>';
			$('#music_file').append(aa);
		});
	});

	getBanben();
  $("#grade").change(function(){
    getBanben();
  });
  $("#term").change(function(){
    getBanben();
  });
  $("#version").change(function(){
    getKecheng();
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
      $('#version').append($("<option>").val(val.r_version).text(val.detail_name));
    });
  });
  getKecheng();
}

function getKecheng()
{
  $("#kecheng").empty();
  var nianji = $("#grade").val();
  var xueqi = $("#term").val();
  var versionid = $("#version").val();
  $.getJSON("../Art/kallKecheng", {nianji:nianji,xueqi:xueqi,versionid:versionid,random:Math.random()}, function(data){
    $.each(data, function(i,val){
      $('#kecheng').append($("<option>").val(val.ks_code).text(val.ks_name));
    });
  });
}



//背景切换时，音频切换
$('#music').change(function(){
	$('#music_file').html('');
	var music_group_id=$('#music').val();
	$.get('../Art/queryMusic',{ran:Math.random(),music_group_id:music_group_id},function(data){
		$.each(data,function(k,v){
			var aa='<option value="'+v.id+'">'+v.music_name+'</option>';
			$('#music_file').append(aa);
		});
	});
});





function savedata(){
		var grade=$('#grade').val();
		var term=$('#term').val();
		var version=$('#version').val();
		var kecheng=$('#kecheng').val();
		var bieming = $('#unit').val();
		//var music=$('#music').val();
		var music_file=$('#music_file').val();
		var moban=$('#moban').val();
		var issuc=$('#issuc').val();
		var filename=$("#filename").val();
		var filepath=$('#filepath').val();
		var types=$('#types').val();
		var flag=false;
		//alert(groupid+groupname+issuc+filename+filepath);
		if(version==''){
			art.dialog.alert('版本不能为空！',function(){
				$('#version').focus();
			});
			return false;
		}else if(kecheng==''){
			art.dialog.alert('课程不能为空！',function(){
				$('#kecheng').focus();
			});
			return false;
		}else{
		$.get('../Art/addKechengInfo',
			{ran:Math.random(),
			 grade:grade,
			 term:term,
			 version:version,
			 kecheng:kecheng,
			 bieming:bieming,
			// music:music,
			 music_file:music_file,
			 moban:moban,
			 filepath:filepath,
			 filename:filename,
			 types:types
			},
			function(data){
				if(data=='已存在'){
					flag=false;
				}else{
					flag=true;
				}
			});
			return flag;
		}

}



/**
 * 文件上传
 */
 $(function() {
		$("#file_upload").uploadify(
				{
					'debug' : false,
 					'buttonText' : '选择文件',
 					'height' : 30,
 					'removeCompleted' : false,
 					'auto' : true,
 					'swf' : '/Public/Home/js/uploadify/uploadify.swf?ran='+Math.random(),
					'uploader' : '../Upload/uploadfiles',
					'width' : 70,
					'fileSizeLimit' : '100MB',
					'fileTypeExts' : '*.mp3',
					'fileTypeDesc' : '请选择mp3文件',
					'multi' : false,
					'removeCompleted':true,
					'removeTimeout':0.5,
					'onUploadSuccess' : function(file, data, response) {
						if (response) {
							$('#' + file.id).find('.data').html(' 上传完毕');
							$('.uploadify-button-text').empty();
							$('.uploadify-button-text').append('上传完毕');
							/**
							issuc:1成功；2失败;
							name:文件原名称；
							size：文件大小；
							ext：文件后缀名；
							savename：上传后文件名
							savepath：保存路径（201504/）
							 **/
							 //alert(data);
							var obj = eval("(" + data + ")");
							if (obj.issuc == 1) {
								//alert(obj.msg.savepath+obj.msg.savename);
								$("#filepath").attr("value",obj.msg.savepath + obj.msg.savename);
								$("#filename").attr("value", obj.msg.name);
								$("#issuc").attr("value", obj.issuc);
							}
						}

					}
				});
	});

</script>