package com
{
	import flash.display.*;
	import flash.events.*;
	import flash.utils.getDefinitionByName;
	import gs.*;
	import gs.easing.*;

	public class ImgSlider extends MovieClip
	{
		private var slider_width:Number;
		private var slider_height:Number;
		private var slider_bg:MovieClip;
		private var currentNum:Number;
		private var imgArr:Array;
		private var record:Record;

		private var rightarrow:MovieClip;
		private var leftarrow:MovieClip;
		private var xuanzestate:XuanZeState;

		public function ImgSlider()
		{
			// constructor code
			currentNum = 0;
			
			record = Main.record;
			
			xuanzestate = new XuanZeState();
			
			slider_bg = new MovieClip();

			addChild(slider_bg);
			
			rightarrow = new rightArrow();
			leftarrow = new leftArrow();
			
			slider_bg.addChild(rightarrow);
			slider_bg.addChild(leftarrow);

			leftarrow.x = -70;
			rightarrow.x = 708;
			rightarrow.y = leftarrow.y = 71;


			rightarrow.addEventListener(MouseEvent.CLICK,rightClick);

			leftarrow.addEventListener(MouseEvent.CLICK,leftClick);
			
			this.addEventListener(Event.ENTER_FRAME,loop);

		}
		
		private function loop(e:Event):void{
			
			if(imgArr.length < 3){
				rightarrow.visible = false;
				leftarrow.visible = false;
				return;
				
			}else if(currentNum == 2){
				leftarrow.visible = false;
				rightarrow.visible = true;
			}else if(currentNum == imgArr.length - 1){
				rightarrow.visible = false;
				leftarrow.visible = true;				
			}else{
				rightarrow.visible = true;
				leftarrow.visible = true;				
				
			}
			
			
		}

		public function init(arr:Array):void
		{

			imgArr = arr;

			for (var i:int = 0; i < arr.length; i++)
			{
				if (i < 3)
				{
					
					var bitmap:Bitmap = biLi(new arr[i][0] as Bitmap);
					var mc:MovieClip = new MovieClip();
					mc.addChild(bitmap);
					mc.x = i * mc.width + i * 38;
					mc.y = 5;
					mc.name = "bit" + i;
					mc.num = i;
					xuanzestate.GlowFilterExample(mc);
					slider_bg.addChild(mc);
					mc.addEventListener(MouseEvent.CLICK,objClick);
					currentNum = i;

				}
				else
				{
					break;
				}
			}
			
			slider_bg.x += Math.abs(leftarrow.x) / 2;
		}

		private function rightClick(e:MouseEvent):void
		{
			
			if (currentNum < imgArr.length - 1)
			{
				rightarrow.removeEventListener(MouseEvent.CLICK,rightClick);
				var str:String = "bit" + String(currentNum - 2);
				var bitmap:MovieClip = slider_bg.getChildByName(str) as MovieClip;
				slider_bg.removeChild(bitmap);
				
				var bitmap2:MovieClip = slider_bg.getChildByName("bit" + String(currentNum - 1)) as MovieClip;

				TweenLite.to(bitmap2, 0.2, {alpha:1, x:0, ease:Back.easeOut, delay:0.2});
				
				var bitmap3:MovieClip = slider_bg.getChildByName("bit" + currentNum) as MovieClip;

				TweenLite.to(bitmap3, 0.2, {alpha:1, x:243, ease:Back.easeOut, delay:0.4, onComplete: onFinishTween, onCompleteParams:[5, bitmap3]});				

			}
		}

		function onFinishTween(argument1:Number, argument2:MovieClip):void
		{
			trace("The tween has finished! argument1 = " + argument1 + ", and argument2 = " + argument2);
			currentNum++;
			
			var bitmap:Bitmap = biLi(new imgArr[currentNum][0] as Bitmap);
			var mc:MovieClip = new MovieClip();
			mc.addChild(bitmap);
			mc.x = 2 * mc.width + 2 * 38;
			mc.y = 5;
			mc.name = "bit" + currentNum;
			mc.num = currentNum;

			slider_bg.addChild(mc);
			mc.addEventListener(MouseEvent.CLICK,objClick);
			xuanzestate.GlowFilterExample(mc);
			rightarrow.addEventListener(MouseEvent.CLICK,rightClick);
			
		}
		
		private function leftClick(e:MouseEvent):void
		{			
			
			if (currentNum > 2)
			{
				leftarrow.removeEventListener(MouseEvent.CLICK,leftClick);
				var str:String = "bit" + String(currentNum);
				var bitmap:MovieClip = slider_bg.getChildByName(str) as MovieClip;
				slider_bg.removeChild(bitmap);
				
				var bitmap2:MovieClip = slider_bg.getChildByName("bit" + String(currentNum - 1)) as MovieClip;

				TweenLite.to(bitmap2, 0.2, {alpha:1, x:bitmap2.x + 243, ease:Back.easeOut, delay:0.2});

				var bitmap3:MovieClip = slider_bg.getChildByName("bit" + String(currentNum - 2)) as MovieClip;

				TweenLite.to(bitmap3, 0.2, {alpha:1, x:bitmap3.x + 243, ease:Back.easeOut, delay:0.4, onComplete: onFinishTween2, onCompleteParams:[5, bitmap3]});				

			}

		}
		
		function onFinishTween2(argument1:Number, argument2:MovieClip):void
		{
			trace("The tween has finished! argument1 = " + argument1 + ", and argument2 = " + argument2);
			trace(currentNum);
			currentNum--;
			
			var bitmap:Bitmap = biLi(new imgArr[currentNum - 2][0] as Bitmap);
			var mc:MovieClip = new MovieClip();
			mc.addChild(bitmap);
			mc.x = 0;
			mc.y = 5;
			mc.name = "bit" + String(currentNum - 2);
			mc.num = currentNum - 2;
						
			slider_bg.addChild(mc);
			mc.addEventListener(MouseEvent.CLICK,objClick);
			xuanzestate.GlowFilterExample(mc);
			leftarrow.addEventListener(MouseEvent.CLICK,leftClick);
			
		}		
		
		private function objClick(e:MouseEvent):void{

				record.buttons.clearstage();
				
				var lunboimg:LunboIMG = new LunboIMG(record.chuangzuoArr,e.currentTarget.num);
				lunboimg.name ="addimg";
				lunboimg.x = (record._w - lunboimg.width) / 2 ;
				lunboimg.y = (record._h - lunboimg.height ) / 2 - 30 ;
				record.main.addChild(lunboimg);													
			
		}
		

		private function biLi(b:Bitmap):Bitmap
		{
			var bili:Number = 205 / b.width;
			b.width = 205;
			b.height = bili * b.height;
			return b;
		}

	}

}