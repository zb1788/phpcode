
/*基础初始化类*/
$.EBC = {
  //动态获取下拉框的内容
  setCourse:function(obj){
    $(obj).empty();
    $.get('../Paging/getCourseinfo',{random:Math.random()}, function(data){
      $.each(data, function(i,value){
         $(obj).append($("<option>").val(value.id).text(value.name));
      });
    });
  },
  //获取多选框选中的内容
   getCheckBox:function(name){
    var arr = [];
    $('input[name='+name+']:checked').each(function(){
        arr.push($(this).val());//把选中的区域放入数组
    });
    var str=arr.join('|');
    if(str==''){
      alert('至少要选择一个！');
      return false;
    }else{
      alert(str);
    }
  }

}

$.fn.fn1 = function()
{
    alert('aa');
}


function isNumber(obj) {
    var re = /^[0-9]+.?[0-9]*$/;
    return re.test(obj);
}


$(".tr:odd").css("background", "#F5F8FA");

$('.tr:odd').live('hover',function(event){
    if(event.type=='mouseenter'){
      $(this).css("background-color", "#E5EBEE");
    }else{
      $(this).css("background-color", "#F5F8FA");
    }
});

$('.tr:even').live('hover',function(event){
    if(event.type=='mouseenter'){
      $(this).css("background-color", "#E5EBEE");
    }else{
      $(this).css("background-color", "#FFF");
    }
});


function checkFile(url){
    $.ajax({
        type:"POST",
        url:url,
        data:{ran:Math.random()},
        success:function(data){},
        error:function(){
            $('.img').children('img').attr('src','../../uploadklx/klxsz/uploads/default.gif');
        }
    });
}


function errorImg(img) {
img.src = "../../uploadklx/klxsz/uploads/default.gif";
img.onerror = null;
}

//将字符串拆成字符，并存到数组中
String.prototype.strToChars = function(){
    var chars = new Array();
    for (var i = 0; i < this.length; i++){
        chars[i] = [this.substr(i, 1), this.isCHS(i)];
    }
    String.prototype.charsArray = chars;
    return chars;
}
//判断某个字符是否是汉字
String.prototype.isCHS = function(i){
    if (this.charCodeAt(i) > 255 || this.charCodeAt(i) < 0)
        return true;
    else
        return false;
}
//截取字符串（从start字节到end字节）
String.prototype.subCHString = function(start, end){
    var len = 0;
    var str = "";
    this.strToChars();
    for (var i = 0; i < this.length; i++) {
        if(this.charsArray[i][1])
            len += 2;
        else
            len++;
        if (end < len)
            return str;
        else if (start < len)
            str += this.charsArray[i][0];
    }
    return str;
}
//截取字符串（从start字节截取length个字节）
String.prototype.subCHStr = function(start, length){
    return this.subCHString(start, start + length);
}
String.prototype.subCHStr3Dot = function(start, length){
    var tmpStr= this.subCHString(start, start + length);
    if(this.length>length)
    {
    tmpStr+="...";
    }
    return tmpStr;
}
//判断是否是IE
function isIE()
{
    if(!!window.ActiveXObject || "ActiveXObject" in window)
        return true;
    else
        return false;
}

/**
 * 验证密码强弱等级开始
 */
function pwStrength(pwd){

        S_level = checkStrong(pwd);

        O_color = "";
        L_color = "ruo";
        M_color = "zhong";
        H_color = "qiang";
        if (pwd == null || pwd == "") {
            Lcolor = O_color;
            Lcolor_H = "";
        }
        else {
            //S_level = checkStrong(pwd);
            switch (S_level) {
                case 0:
                    Lcolor = O_color;
                    Lcolor_H = "";
                case 1:
                    Lcolor = L_color;
                    Lcolor_H = "弱";
                    break;
                case 2:
                    Lcolor = M_color;
                    Lcolor_H = "中";
                    break;
                default:
                    Lcolor = H_color;
                    Lcolor_H = "强";
            }
        }
        alert(Lcolor_H);
        //$(".grade-pwd ul").removeClass().addClass(Lcolor);
        //$(".grade-pwd .grade-text").html(Lcolor_H);
}

function checkStrong(sPW) {
        if (sPW.length <= 4)
            return 0; //密码太短
        Modes = 0;
        for (i = 0; i < sPW.length; i++) {
            //测试每一个字符的类别并统计一共有多少种模式.
            Modes |= CharMode(sPW.charCodeAt(i));
        }
        return bitTotal(Modes);
}

function CharMode(iN) {
        if (iN >= 48 && iN <= 57) //数字
            return 1;
        if (iN >= 65 && iN <= 90) //大写字母
            return 2;
        if (iN >= 97 && iN <= 122) //小写
            return 4;
        else
            return 8; //特殊字符
}
function bitTotal(num) {
        modes = 0;
        for (i = 0; i < 4; i++) {
            if (num & 1) modes++;
            num >>= 1;
        }
        return modes;
}
/**
 * 验证密码强弱等级结束
 */



/**
 *
 *                     _oo0oo_
 *                    o8888888o
 *                    88" . "88
 *                    (| -_- |)
 *                    0\  =  /0
 *                  ___/`---'\___
 *                .' \\|     |// '.
 *               / \\|||  :  |||// \
 *              / _||||| -:- |||||- \
 *             |   | \\\  -  /// |   |
 *             | \_|  ''\---/''  |_/ |
 *             \  .-\__  '-'  ___/-. /
 *           ___'. .'  /--.--\  `. .'___
 *        ."" '<  `.___\_<|>_/___.' >' "".
 *       | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *       \  \ `_.   \_ __\ /__ _/   .-` /  /
 *   =====`-.____`.___ \_____/___.-`___.-'=====
 *                     `=---='
 *
 *   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *             佛祖开光         永无BUG
 *
 */