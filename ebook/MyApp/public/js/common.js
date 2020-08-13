
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