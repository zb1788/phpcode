package com.btn {
	
	import flash.events.*;
	
	import flash.display.*;
	
	import com.*;
	
	public class ZhiShiChuang extends MovieClip{
		
		private var cttArr:Array = new Array();			//内容数组
		
		private var currNum:int;
		
		private var bitmap:Bitmap;						//实例化数组中图片
		
		private var container:MovieClip;				//父对象 图片居中对齐要用到
		
		private var daanjianjie:MovieClip;				//答案与简介
		
		public function ZhiShiChuang() {
			
		}

		/*
		* 初始化函数
		* 内容数据，父对象,当前页数
		*/
		public function init(_arr:Array,ctn:MovieClip,_num:int = 0,_name:String="null") {

			cttArr = _arr;
			
			currNum = _num;			//翻到第一张图片
			
			container = ctn;

			if(cttArr[currNum].length == 1){
				
				bitmap = new cttArr[currNum][0] as Bitmap;
				
				bitmap.smoothing = true;
				
				ratio(bitmap,525);
				
				bitmap.x = (container.width - bitmap.width) / 2;
				
				container.addChild(bitmap);
				
			}else{
				
				bitmap = new cttArr[currNum][0] as Bitmap;
				
				bitmap.smoothing = true;
				
				ratio(bitmap,400);
				
				bitmap.x = (container.width - bitmap.width) / 2;
				
				container.addChild(bitmap);
				
				var daanjianjie:MovieClip = new DaAnJianJie(cttArr[currNum][1],cttArr[currNum][2]);
				
				container.addChild(daanjianjie);
				
				daanjianjie.x = 63;
				
				daanjianjie.y = 365;
				
			}

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
