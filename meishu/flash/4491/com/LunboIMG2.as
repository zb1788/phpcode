package com {
	
	import flash.display.*;
	import flash.events.*;
	import flash.text.*;
	
	public class LunboIMG2 extends MovieClip{
		
		private var leftarrow:MovieClip;
		private var rightarrow:MovieClip;
		private var imgMc:MovieClip;
		private var arrimg:Array;
		private var num:int;
		private var txtarr:Array;

		public function LunboIMG2(arr:Array,_imgnum:int = 0) {

				if(!(arr[0] is Class)){
					arrimg = new Array();
					txtarr = new Array();
					for(var i:int = 0 ;i < arr.length; i++){

						arrimg.push(arr[i][0]);
						var _num:int = arr[i].length - 1;
						txtarr.push(arr[i][_num]);
					}
				}else{
					arrimg = arr;
				}
				
			num = _imgnum;
				
				//创建左箭头
				leftarrow = new leftArrow();
				leftarrow.x = 0;
				leftarrow.y = 250;
				this.addChild(leftarrow);
				leftarrow.addEventListener(MouseEvent.CLICK,leftClick);
			
				//创建右箭头
				rightarrow = new rightArrow();
				rightarrow.x = 800;
				rightarrow.y = 250;		
				this.addChild(rightarrow);
				rightarrow.addEventListener(MouseEvent.CLICK,rightClick);
				
			
			//图片容器
			imgMc = new MovieClip();
			imgMc.x = 50;
			imgMc.y = 0;
			this.addChild(imgMc);
			

			var bit:Bitmap = new arrimg[num] as Bitmap;
			bit.smoothing = true;
			createBit(bit);
			
			this.addEventListener(Event.ENTER_FRAME,loop);
			
		}
		
		private function loop(e:Event):void{
			
			if(arrimg.length > 1){
				rightarrow.visible = true;
				leftarrow.visible = true;			
			}else {
				rightarrow.visible = false;
				leftarrow.visible = false;							
				return;				
			}			
			
            if(num == arrimg.length - 1){
				rightarrow.visible = false;
			}else if(num <= 0){
				leftarrow.visible = false;
			}else{
				
				rightarrow.visible = true;
				leftarrow.visible = true;			
				
			}
			
		}
		
		//左按钮单击
		private function leftClick(e:MouseEvent):void{
			
			if(num > 0){
				num--;
				var bit:Bitmap = new arrimg[num] as Bitmap;
				bit.smoothing = true;
				createBit(bit);
				
			}
		}
		
		//右按钮单击
		private function rightClick(e:MouseEvent):void{
			
			if(num < arrimg.length - 1){
				num++;
				var bit:Bitmap = new arrimg[num] as Bitmap;
				bit.smoothing = true;
				createBit(bit);
			}  
		}

		// 移除容器所有图像 并添加新图像
		private function createBit(b:Bitmap):void{
			while(imgMc.numChildren>0){
				
				imgMc.removeChildAt(0); 

			}
			var bit:MovieClip = new MovieClip();
			bit.addChild(imgBiLi(b));
			//var bit:Bitmap = imgBiLi(b);
			
			imgMc.addChild(bit);
			bit.x = (this.width - bit.width) / 2 - 50 ;
			bit.y = (this.height - bit.height ) / 2 ;			

	            var label:TextField = new TextField();
	            label.autoSize = TextFieldAutoSize.LEFT;
	          //  label.background = true;
	         //  label.border = true;

	            var format:TextFormat = new TextFormat();
	            format.font = "微软雅黑";
	            format.color = 0x000000;
	            format.size = 25;
				format.align = TextFormatAlign.CENTER;
	          //  format.underline = true;

	            label.defaultTextFormat = format;
				label.x = bit.x;
				label.y = bit.y + bit.height + 10;
				label.width = bit.width;
				label.mouseEnabled = false;
				label.wordWrap = true;

				if(txtarr == null){
					label.text = "  ";
			    }else if(txtarr[num]==""){

					label.text = " ";
					
				}else{

					label.text = txtarr[num];
				}
				
	            imgMc.addChild(label);											
				
			
			
		}
		
		private function configureLabel():void {

        }
		
		//图片比例
		private function imgBiLi(b:Bitmap):Bitmap{
			var _w:Number;
			var _h:Number;
			trace("图片原始宽高：" + b.width +"  " + b.height)
			if(b.width > b.height){
				_w = 660 / b.width;

				b.width = b.width * _w;
				b.height = b.height * _w;
				
			}else{
				_h = 490 / b.height;

				b.width = b.width * _h;
				b.height = b.height * _h;
			}
			
			return b;
		}

	}
	
}
