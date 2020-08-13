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
			//如果移动的长度超过50px;就处理。
			var zhi=move.x-point.x;
			if(Math.abs(zhi)>50&&move.x>0)
			{
				if(zhi>0)
					$("#xinxi").html("向右滑动");
				else $("#xinxi").html("向左滑动");
			}
		}
	}
});
</script>
