<script type="text/javascript">
var mousedown=false;
var point={};
var move={};
$("#slide").bind({
	mousedown:function (event) {
		event.stopPropagation();
		$("#xinxi").html("");
		mousedown=true;
		point.x = event.pageX;
		move.x=0;
	},
	mousemove:function(event){
		event.preventDefault();
		event.stopPropagation();
		if(mousedown){
			move.x=event.pageX;
		}
	},
	mouseup:function(event){
		event.stopPropagation();
		if(mousedown)
		{
			mousedown=false;
			//����ƶ��ĳ��ȳ���50px;�ʹ���
			var zhi=move.x-point.x;
			if(Math.abs(zhi)>50&&move.x>0)
			{
				if(zhi>0)
					$("#xinxi").html("���һ���");
				else $("#xinxi").html("���󻬶�");
			}
		}
	}
});
</script>
