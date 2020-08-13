//选中指定结果
$('#zhiding').live('click',function(){
	if(this.checked){
		$(this).parent().next().next().next().next().show();
		$('#result_min').val('');
		$('#result_max').val('');
		$(this).parent().next().next().next().next().next().hide();
	}
});
//选定范围结果
$('#fanwei').live('click',function(){
	if(this.checked){
		$(this).parent().next().next().hide();
		$('#result_zhiding').val('');
		$(this).parent().next().next().next().show();
	}
});
//点击删除
$('input[name="del"]').live('click',function(){
	var xuhao=$(this).parent().parent().prevAll().length;
	$.each($('#table_data tr:gt('+xuhao+')'),function(k,v){
		var index=$(v).children('td').eq(0).html();
		$(v).children('td').eq(0).html(index-1);
	});
	$(this).parent().parent().remove();
	showresult();
});

//随机排序
$('#sort').live('click',function(){
	var arr=[];
	$('#table_data tr td:nth-child(2)').each(function(k,v){
		arr.push($(v).html());
	});
	arr.sort(function(){ return 0.5 - Math.random() });
	insertTable(arr);
	showresult();
});

//插入table结果
function insertTable(data){
  	$('#table_data tr:not(:eq(0))').remove();
	$.each(data,function(k,v){
		var tr=$('#demo tr').eq(0).clone();
		tr.children('td').eq(0).html(k+1);
		tr.children('td').eq(1).html(v);
		var arr=[];
		arr=v.split('=');
		tr.children('td').eq(2).html(arr[1]);
		tr.appendTo('#table_data');
	});
}

//显示结果
//$('#showresult').click(function(){
//	showresult();
//});
//显示结果函数
function showresult(){
	var arr=[];
	$('#table_data tr td:nth-child(3)').each(function(k,v){
		arr.push($(v).html());
	});
	var sum=arr.length;
	var str='结果（'+sum+'）个：'+arr.join(',');
	$('#resultInfo').find('td').html(str);
	$('#resultInfo').show();
}
