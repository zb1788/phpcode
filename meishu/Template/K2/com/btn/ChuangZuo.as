package com.btn {
	
	import flash.display.*;
	import flash.events.*;
	import flash.utils.getDefinitionByName;
	import com.LunBoImg;
	import com.XuanZeState;
	import gs.*;
	import gs.easing.*;	
	import flash.text.*;
	
	public class ChuangZuo extends MovieClip{
		
		public var lunboimg:LunBoImg;
		
		private var currNum:int;
		
		private var cttArr:Array = new Array();			//内容数组
		
		private var bitmap:Bitmap;						//实例化数组中图片
		
		private var container:MovieClip;				//父对象 图片居中对齐要用到
		
		private var leftarrow:MovieClip = new LeftArrow();		//向左按钮
			
		private var rightarrow:MovieClip = new RightArrow();	//向右按钮		
		
		private var xuzestate:XuanZeState = new XuanZeState();	
		
		private var textfield:TextField;					//文本说明2
		

		public function ChuangZuo() {
			// constructor code
		}
		
		/*
		* 初始化函数
		* 内容数据，父对象,当前页数
		*/
		public function init(_arr:Array,ctn:MovieClip,_num:int = 0,_name:String ="null"):void {
			
			cttArr = _arr;
			
			container = ctn;			
			
			currNum = _num;
			
			if(_name == "yishuchuangzuo"){
				
				lunbo(cttArr,container,currNum);
				
				return;
				
			}			
			
			//创建文本框说明1
			var chuangzuotxt:MovieClip = new chuangzuoTxt();
			
			container.addChild(chuangzuotxt);
			
			chuangzuotxt.txt.text = Main.record.chuangzuoshuoming;
			
			chuangzuotxt.y = 20;
			
			chuangzuotxt.x = 45;
			
			for (var i:int = 0; i < cttArr.length; i++)
			{
				if (i < 3)
				{
					
					bitmap = ratio(new cttArr[i][0] as Bitmap);
					
					var mc:MovieClip = new MovieClip();
					
					mc.addChild(bitmap);
					
					mc.buttonMode = true;
					
					var _num:int = (container.width - (mc.width * 3 + 38 * 2) ) / 2 ;
					
					mc.x =  _num + i * mc.width + i * 38;
					
					mc.y = 170;
					
					mc.name = "bit" + i;
					
					mc.num = i;
					
					xuzestate.GlowFilterExample(mc);
					
					container.addChild(mc);
					
					mc.addEventListener(MouseEvent.CLICK,objClick);
					
					currNum = i;

				}
				else
				{
					break;
				}
			}
			
			//创建 左右箭头  ，此处不能用公用那个函数，因为这里有缓动 动画
			var ctn:MovieClip = container.parent as MovieClip;
			
			leftarrow.name = "leftarrow1";
			
			rightarrow.name = "rightarrow1";
			
			ctn.addChild(leftarrow);
			
			ctn.addChild(rightarrow);
			
			leftarrow.x = container.x - leftarrow.width;
			
			rightarrow.x = container.width + container.x ;
			
			rightarrow.y = leftarrow.y = 405;
			
			leftarrow.addEventListener(MouseEvent.CLICK,leftClick);
			
			rightarrow.addEventListener(MouseEvent.CLICK,rightClick);
			
			//文本说明
			textfield = new TextField();
			
			textfield.width = 645;
			
			textfield.height = 80;
			
			textfield.defaultTextFormat = new TextFormat("微软雅黑",28,0x000000,null,null,null,null,null,TextFormatAlign.CENTER);
			
			textfield.x = (container.width - textfield.width) / 2;
			
			textfield.y = 170 + bitmap.height + 20;
			
			textfield.text = Main.record.chuangzuoshuoming2;
			
			textfield.wordWrap = true;
			
			container.addChild(textfield);			
			
			
			//循环检查 箭头是否可用
			this.addEventListener(Event.ENTER_FRAME,loop);

		}
		
		private function loop(e:Event):void{
			
			if(cttArr.length <= 3){
				
				rightarrow.visible = false;
				
				leftarrow.visible = false;
				
				return;
				
			}else if(currNum == 2){
				
				leftarrow.visible = false;
				
				rightarrow.visible = true;
				
			}else if(currNum == cttArr.length - 1){
				
				rightarrow.visible = false;
				
				leftarrow.visible = true;				
				
			}else{
				
				rightarrow.visible = true;
				
				leftarrow.visible = true;				
				
			}
			
		}		
		
		/*
		* 向左箭头 单击事件
		*/
		private function leftClick(e:MouseEvent):void{
			
			if (currNum > 2)
			{
				leftarrow.removeEventListener(MouseEvent.CLICK,leftClick);
				
				//图像名称
				var str:String = "bit" + String(currNum);
				
				//实例化图像
				var bitmap1:MovieClip = container.getChildByName(str) as MovieClip;
				
				container.removeChild(bitmap1);
				
				var bitmap2:MovieClip = container.getChildByName("bit" + String(currNum - 1)) as MovieClip;

				TweenLite.to(bitmap2, 0.2, {alpha:1, x:bitmap2.x + 243, ease:Back.easeOut, delay:0.2});

				var bitmap3:MovieClip = container.getChildByName("bit" + String(currNum - 2)) as MovieClip;

				TweenLite.to(bitmap3, 0.2, {alpha:1, x:bitmap3.x + 243, ease:Back.easeOut, delay:0.4, onComplete: onFinishTween2, onCompleteParams:[5, bitmap3]});				

			}
			
		}
		
		/*
		* 向右箭头 单击事件
		*/
		private function rightClick(e:MouseEvent):void{
			
			if (currNum < cttArr.length - 1)
			{
				rightarrow.removeEventListener(MouseEvent.CLICK,rightClick);
				
				var str:String = "bit" + String(currNum - 2);
				
				var bitmap1:MovieClip = container.getChildByName(str) as MovieClip;
				
				container.removeChild(bitmap1);
				
				var bitmap2:MovieClip = container.getChildByName("bit" + String(currNum - 1)) as MovieClip;
				
				var _num:int = (container.width - (bitmap2.width * 3 + 38 * 2) ) / 2 ;

				TweenLite.to(bitmap2, 0.2, {alpha:1, x:_num, ease:Back.easeOut, delay:0.2});
				
				var bitmap3:MovieClip = container.getChildByName("bit" + currNum) as MovieClip;

				TweenLite.to(bitmap3, 0.2, {alpha:1, x:_num + 205 + 38, ease:Back.easeOut, delay:0.4, onComplete: onFinishTween, onCompleteParams:[5, bitmap3]});				

			}			
			
		}		
		
		private function onFinishTween(argument1:Number, argument2:MovieClip):void
		{
			trace("The tween has finished! argument1 = " + argument1 + ", and argument2 = " + argument2);
			
			currNum++;
			
			var bitmap1:Bitmap = ratio(new cttArr[currNum][0] as Bitmap);
			
			var mc:MovieClip = new MovieClip();
			
			mc.buttonMode = true;
			
			mc.addChild(bitmap1);
			
			var _num:int = (container.width - (mc.width * 3 + 38 * 2) ) / 2 ;
					
			mc.x =  _num + 2 * mc.width + 2 * 38;			
			
			mc.y = 170;
			
			mc.name = "bit" + currNum;
			
			mc.num = currNum;

			container.addChild(mc);
			
			mc.addEventListener(MouseEvent.CLICK,objClick);
			
			xuzestate.GlowFilterExample(mc);
			
			rightarrow.addEventListener(MouseEvent.CLICK,rightClick);
			
		}		
		
		private function onFinishTween2(argument1:Number, argument2:MovieClip):void
		{
			
			trace("The tween has finished! argument1 = " + argument1 + ", and argument2 = " + argument2);

			currNum--;
			
			var bitmap1:Bitmap = ratio(new cttArr[currNum - 2][0] as Bitmap);
			
			var mc:MovieClip = new MovieClip();
			
			mc.buttonMode = true;
			
			mc.addChild(bitmap1);
			
			var _num:int = (container.width - (mc.width * 3 + 38 * 2) ) / 2 ;
					
			mc.x =  _num ;
			
			mc.y = 170;
			mc.name = "bit" + String(currNum - 2);
			mc.num = currNum - 2;
						
			container.addChild(mc);
			
			mc.addEventListener(MouseEvent.CLICK,objClick);
			
			xuzestate.GlowFilterExample(mc);
			
			leftarrow.addEventListener(MouseEvent.CLICK,leftClick);
			
		}				
		
		/*
		* 图片单击
		*/
		private function objClick(e:MouseEvent):void{
			
			var num:int = e.currentTarget.num;
			
			trace("您单击了此序列图像: " + num  +" 对象的父容器是： " + container.parent);
			
			MovieClip(container.parent).directionBtn(num,cttArr.length);	
			
			MovieClip(container.parent).currNum = num;
			
			lunbo(cttArr,container,num);
			
		}		
		
		/*
		* 调整图片比例
		*/
		private function ratio(b:Bitmap):Bitmap
		{
			var bili:Number = 205 / b.width;
			
			b.width = 205;
			
			b.height = bili * b.height;
			
			return b;
		}
		
		/*
		* 图片轮播
		*/
		private function lunbo(_cttarr:Array,_container:MovieClip,_int:int):void{
			
			MovieClip(container.parent).clearStage();
			
			lunboimg = new LunBoImg();
			
			lunboimg.init(_cttarr,_container,_int,"yishuchuangzuo");			
			
		}


	}
	
}
