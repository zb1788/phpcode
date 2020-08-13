<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>网页MP4播放器酷播迷你CuPlayerMiniV4(免费版)下载</title>
<meta name="keywords" content="网页播放器" />
<meta name="description" content="网页播放器" />
<link rel="shortcut icon" href="/favicon.ico" >
<style type="text/css">
body {margin:0px; padding:20px;  margin-top:0px;font-size:12px; color:#313131; }
div,ul,li,ol,dl,dt,dd,form,img,p{margin: 0; padding: 0; border: 0; }
li{list-style-type:none;}
h2,h4,h5{margin: 0; padding: 0; font-size:14px;}
div.help { line-height:26px; font-size:14px;}
</style>
</head>

<body>

<!-- 我爱播放器(52player.com)/代码开始 -->
<script type="text/javascript" src="images/swfobject.js"></script>
<script type="text/javascript">
//alert('uploads/'+GetQueryString("filepath"));
function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}
</script>
<div class="video" id="CuPlayer">
<strong>我爱播放器(52player.com) 提示：您的Flash Player版本过低，请<a href="http://www.52player.com/">点此进行网页播放器升级</a>！</strong></div>
<script type="text/javascript">
var so = new SWFObject("CuPlayerMiniV4.swf","CuPlayerV4","600","410","9","#000000");
so.addParam("allowfullscreen","true");
so.addParam("allowscriptaccess","always");
so.addParam("wmode","opaque");
so.addParam("quality","high");
so.addParam("salign","lt");
so.addVariable("CuPlayerSetFile","CuPlayerSetFile.php"); //播放器配置文件地址,例SetFile.xml、SetFile.asp、SetFile.php、SetFile.aspx
so.addVariable("CuPlayerFile",'../../../Uploads/'+GetQueryString("filepath")); //视频文件地址
so.addVariable("CuPlayerImage","images/start.jpg");//视频略缩图,本图片文件必须正确
so.addVariable("CuPlayerWidth","600"); //视频宽度
so.addVariable("CuPlayerHeight","410"); //视频高度
so.addVariable("CuPlayerAutoPlay","yes"); //是否自动播放
so.addVariable("CuPlayerLogo","images/logo.png"); //Logo文件地址
so.addVariable("CuPlayerPosition","bottom-right"); //Logo显示的位置
so.write("CuPlayer");
</script>
<!-- 我爱播放器(52player.com)/代码结束 -->



</body>
</html>




