/**
 *	LocalStorage类
 */

var storage = function() {
    //初始化函数
    this.checkLocalStorage = function() {
        if (!window.localStorage) {
            alert("您的手机不支持预览功能");
            return false;
        }
    } ()
    /**
     * 存储内容到localStorage
     * 【主要】如果是json格式需要encodeURI(JSON.stringify(value)),取(JSON.parse())的时候decodeURI(value)
     * @param {[String]} name  [名称]
     * @param {[String]} value [值]
     */
    this.set = function(name, value) {
        window.localStorage.setItem(name, value);
    }
    //获取指定key的内容
    this.get = function(name) {
        return decodeURI(window.localStorage.getItem(name));
    }

    //清空单个值
    this.remove = function(name) {
        window.localStorage.removeItem(name);
    }

    //清空所有值（尽量不要用，以免把别人的缓存清掉）
    this.removeAll = function() {
        window.localStorage.clear();
    }
}

// 删除数组中数据 
Array.prototype.del = function(n) {
    if (n < 0 || typeof(n) == 'undefined') return this;
    return this.slice(0, n).concat(this.slice(n + 1, this.length));
}
// 数组洗牌 
Array.prototype.random = function() {
    var nr = [],
    me = this,
    t;
    while (me.length > 0) {
        nr[nr.length] = me[t = Math.floor(Math.random() * me.length)];
        me = me.del(t);
    }
    return nr;
}
// 数字数组排序 
Array.prototype.sortNum = function(f) {
    if (!f) f = 0;
    if (f == 1) return this.sort(function(a, b) {
        return b - a;
    });
    return this.sort(function(a, b) {
        return a - b;
    });
}
// 获得数字数组的最大项 
Array.prototype.getMax = function() {
    return this.sortNum(1)[0];
}
// 获得数字数组的最小项 
Array.prototype.getMin = function() {
    return this.sortNum(0)[0];
}
// 数组第一次出现指定元素值的位置 
Array.prototype.indexOf = function(o) {
    for (var i = 0; i < this.length; i++) if (this[i] == o) return i;
    return - 1;
}
// 移除数组中重复的项 
Array.prototype.removeRepeat = function() {
    this.sort();
    var rs = [];
    var cr = false;
    for (var i = 0; i < this.length; i++) {
        if (!cr) cr = this[i];
        else if (cr == this[i]) rs[rs.length] = i;
        else cr = this[i];
    }
    var re = this;
    for (var i = rs.length - 1; i >= 0; i--) re = re.del(rs[i]);
    return re;
}

//替换字符串，全部替换
String.prototype.replaceAll = function(s1, s2) {
    return this.replace(new RegExp(s1, "gm"), s2);
}
//获取字符数组 
String.prototype.ToCharArray = function() {
    return this.split("");
}
//获取N个相同的字符串 
String.prototype.Repeat = function(num) {
    var tmpArr = [];
    for (var i = 0; i < num; i++) tmpArr.push(this);
    return tmpArr.join("");
}
//逆序 
String.prototype.Reverse = function() {
    return this.split("").reverse().join("");
}
//测试是否是数字 
String.prototype.IsNumeric = function() {
    var tmpFloat = parseFloat(this);
    if (isNaN(tmpFloat)) return false;
    var tmpLen = this.length - tmpFloat.toString().length;
    return tmpFloat + "0".Repeat(tmpLen) == this;
}
//测试是否是整数 
String.prototype.IsInt = function() {
    if (this == "NaN") return false;
    return this == parseInt(this).toString();
}
// 合并多个空白为一个空白 
String.prototype.resetBlank = function() {
    return this.replace(/s+/g, " ");
}
// 除去左边空白 
String.prototype.LTrim = function() {
    return this.replace(/^s+/g, "");
}
// 除去右边空白 
String.prototype.RTrim = function() {
    return this.replace(/s+$/g, "");
}
// 除去两边空白 
String.prototype.trim = function() {
    return this.replace(/(^s+)|(s+$)/g, "");
}
// 保留数字 
String.prototype.getNum = function() {
    return this.replace(/[^d]/g, "");
}
// 保留字母 
String.prototype.getEn = function() {
    return this.replace(/[^A-Za-z]/g, "");
}
// 保留中文 
String.prototype.getCn = function() {
    return this.replace(/[^u4e00-u9fa5uf900-ufa2d]/g, "");
}
// 得到字节长度 
String.prototype.getRealLength = function() {
    return this.replace(/[^x00-xff]/g, "--").length;
}
// 从左截取指定长度的字串 
String.prototype.left = function(n) {
    return this.slice(0, n);
}
// 从右截取指定长度的字串 
String.prototype.right = function(n) {
    return this.slice(this.length - n);
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
// HTML编码 
String.prototype.HTMLEncode = function() {
    var re = this;
    var q1 = [/x26/g, /x3C/g, /x3E/g, /x20/g];
    var q2 = ["&", "<", ">", " "];
    for (var i = 0; i < q1.length; i++) re = re.replace(q1[i], q2[i]);
    return re;
}
// Unicode转化 
String.prototype.ascW = function() {
    var strText = "";
    for (var i = 0; i < this.length; i++) strText += "&#" + this.charCodeAt(i) + ";";
    return strText;
}
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