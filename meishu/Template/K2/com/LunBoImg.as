package com {
	
	import flash.display.*;
	import flash.events.*;
	import flash.text.*;
	
	public class LunBoImg {
		
		private var cttArr:Array = new Array();			//内容数组
		
		private var currNum:int;
		
		private var bitmap:Bitmap;						//实例化数组中图片
		
		private var container:MovieClip;				//父对象 图片居中对齐要用到		
		
		private var textfield:TextField;

		public function LunBoImg() {
			// constructor code
			
		}
		
		public function init(_arr:Array,ctn:MovieClip,_num:int = 0,_name:String ="null"):void{
			
			cttArr = _arr;
			
			currNum = _num;			//翻到第一张图片
			
			container = ctn;

			bitmap = new cttArr[currNum][0] as Bitmap;
				
			bitmap.smoothing = true;
				
			ratio(bitmap);
				
			bitmap.x = (container.width - bitmap.width) / 2;
				
			container.addChild(bitmap);			
			
			//文本说明
			textfield = new TextField();
			
			textfield.width = 645;
			
			textfield.height = 80;
			
			textfield.defaultTextFormat = new TextFormat("微软雅黑",28,0x000000,null,null,null,null,null,TextFormatAlign.CENTER);
			
			textfield.x = (container.width - textfield.width) / 2;
			
			textfield.y = 492;
			
			if(_name == "yishuchuangzuo"){
				
				textfield.text = cttArr[currNum][1];
				
			}else{
				
				textfield.text = cttArr[currNum][9];
				
			}
			
			textfield.wordWrap = true;
			
			container.addChild(textfield);			
		}
		
		/*
		* 调整比例
		* 图像 
		*/
		private function ratio(b:Bitmap):Bitmap{
			
			var _w:int = 645;
			
			var _h:int = 485;
			
			var ratio:Number;
			
			if(b.width > b.height){
				
				ratio = _w / b.width;
				
			}else{
				
				ratio = _h / b.height;
				
			}

			b.width *= ratio;
			
			b.height *= ratio;
			
			return b;
		}

	}
	
}
