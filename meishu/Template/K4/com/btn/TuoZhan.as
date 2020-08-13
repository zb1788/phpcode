package com.btn {

	import flash.events.*;
	
	import flash.display.*;
	
	import com.*;	
	
	public class TuoZhan extends MovieClip{
		
		private var cttArr:Array = new Array();			//内容数组
		
		private var currNum:int;
		
		private var bitmap:Bitmap;						//实例化数组中图片
		
		private var container:MovieClip;				//父对象 图片居中对齐要用到		

		public function TuoZhan() {
			// constructor code
		}
		
		/*
		* 初始化函数
		* 内容数据，父对象,当前页数
		*/
		public function init(_arr:Array,ctn:MovieClip,_num:int = 0,_name:String="null") {

			cttArr = _arr;
			
			currNum = _num;			//翻到第一张图片
			
			container = ctn;

			bitmap = new cttArr[currNum] as Bitmap;
				
			bitmap.smoothing = true;
				
			ratio(bitmap,525);
				
			bitmap.x = (container.width - bitmap.width) / 2;
				
			container.addChild(bitmap);

		}
		
		private function ratio(b:Bitmap,_num:int):Bitmap{
			
			var h:int = _num;
			
			var ratio:Number;
			
			if(b.height > h){
				
				ratio = h / b.height;
			}
			
			b.width *= ratio;
			
			b.height *= ratio;
			
			return b;
		}

	}
	
}
