package com.btn {
	
	import flash.display.*;
	import flash.events.*;
	import flash.utils.getDefinitionByName;
	import com.LunBoImg;
	import com.XuanZeState;
	import flash.geom.Point;
	
	public class QvTuYuan extends MovieClip{
		
		public var lunboimg:LunBoImg;
		
		private var currNum:int;
		
		private var cttArr:Array = new Array();			//内容数组
		
		private var bitmap:MovieClip;						//实例化数组中图片
		
		private var container:MovieClip;				//父对象 图片居中对齐要用到
		
		private var maskMc:MovieClip = null;					//遮罩对象
		
		private var xuzestate:XuanZeState = new XuanZeState();
		
		private var bitpoint:Point;
		
		private var maskpoint:Point;

		public function QvTuYuan() {
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
			
			if(_name == "qvtuyuan"){
				
				lunbo(cttArr,container,currNum);
				
				return;
				
			}
			
			for(var i:int = 0; i < cttArr.length ; i++){
				
				var _obj:Object = cttArr[i];

				var bit:Bitmap = new _obj[0] as Bitmap;
				
				bit.smoothing = true;
				
				bitmap = new MovieClip();
				
				bitmap.addChild(bit);
				
				container.addChild(bitmap);
				
				bitmap.num = i;
				
				bitmap.addEventListener(MouseEvent.CLICK,bitClick);
				
				bitmap.x = _obj[3] - container.x;
				
				bitmap.y = _obj[4] - container.y;
				
				bitmap.width = _obj[1];
				
				bitmap.height = _obj[2];
				
				bitmap.name ="img";
				
				xuzestate.GlowFilterExample(bitmap);
				
				// 实例化遮罩对象
				if(maskMc == null){
					
					var maskparent:String = _obj[8];
				
					var masknum:int = maskparent.indexOf(" ");
				
					maskparent = maskparent.substr(masknum + 1,maskparent.length - 2 - masknum);				

					var _class:Class = getDefinitionByName(maskparent) as Class;

					maskMc = new _class;
	
					maskMc.name = "mask";					
				
					container.addChild(maskMc);					
					
					maskMc.x -= container.x;
					
					maskMc.y -= container.y;

				}
				
				//获取遮罩中的单个对象 
				var subobject:MovieClip = maskMc.getChildByName(_obj[5]) as MovieClip;
				
				bitmap.mask = subobject;
				
				bitmap.buttonMode = true;

			}
			
		}
		
		/*
		* 遮罩单击事件
		*/
		private function bitClick(e:MouseEvent):void{
			
			var num:int = e.currentTarget.num;
			
			trace("您单击了此序列图像: " + num  +" 对象的父容器是： " + container.parent);
			
			MovieClip(container.parent).directionBtn(num,cttArr.length);	
			
			MovieClip(container.parent).currNum = num;
			
			lunbo(cttArr,container,num);
			
		}		
		
		/*
		* 图片轮播
		*/
		private function lunbo(_cttarr:Array,_container:MovieClip,_int:int):void{
			
			MovieClip(container.parent).clearStage();
			
			lunboimg = new LunBoImg();
			
			lunboimg.init(_cttarr,_container,_int);			
			
		}
		

	}
	
}
