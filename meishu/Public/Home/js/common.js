
/*基础初始化类*/
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



 /*
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
 *
 */