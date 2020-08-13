package com {
	import flash.display.Bitmap;
	import flash.display.MovieClip;
	import flash.events.*;
	import flash.utils.getDefinitionByName;
	
	public class QvTu extends MovieClip {
		
		private var record:Record;
		private var xuanzestate:XuanZeState;
        private var shape:MovieClip = null;
		
		public function QvTu(arr:Array) {
			// constructor code
			
			record = Main.record;
			
			xuanzestate  = new XuanZeState();
			trace(arr);
			for(var i:int = 0; i < arr.length ; i++){
				var _obj:Object = arr[i];
				trace(_obj[0]);
				var _bit:Bitmap = new _obj[0] as Bitmap;
				var bit:MovieClip = new MovieClip();
				bit.addChild(_bit);
				bit.num = i;
				bit.addEventListener(MouseEvent.CLICK,bitClick);
				bit.x = _obj[3];
				bit.y = _obj[4];
				bit.width = _obj[1];
				bit.height = _obj[2];
				bit.name ="img";
				
				xuanzestate.GlowFilterExample(bit);
				if(shape == null){
					trace(_obj[8]);
				var maskparent:String = _obj[8];
				var masknum:int = maskparent.indexOf(" ");
				maskparent = maskparent.substr(masknum + 1,maskparent.length - 2 - masknum);				

				
				var _class:Class = getDefinitionByName(maskparent) as Class;

				shape = new _class;

				shape.name = "mask";					
					
				}

				var subobject:MovieClip = shape.getChildByName(_obj[5]) as MovieClip;

				bit.mask = subobject;
				if(record.maskMc == null){
					record.maskMc = new MovieClip();
					record.maskMc.name = "img";
					record.main.addChild(record.maskMc);
				}

				shape.addChild(bit);
				shape.setChildIndex(bit,0);
				record.maskMc.addChild(shape);

			}
			
		}
		
		//var xuanzestate:XuanZeState = new XuanZeState();;
		private function myover(e:MouseEvent):void{

			
		}
		
		private function myout(e:MouseEvent):void{
			
			
			
		}
		
		private function bitClick(e:MouseEvent):void{
			trace(e.currentTarget.num);

			if(record.qvtu_huazhan == "趣图"){
				record.buttons.clearstage();
				
				var lunboimg:LunboIMG = new LunboIMG(record.qvtuArr,e.currentTarget.num);
				lunboimg.name ="addimg";
				lunboimg.x = (record._w - lunboimg.width) / 2 ;
				lunboimg.y = (record._h - lunboimg.height ) / 2 - 30 ;
				record.main.addChild(lunboimg);										
				
			}else if(record.qvtu_huazhan == "画展"){
				
				record.buttons.clearstage();
				
				var lunboimg2:LunboIMG = new LunboIMG(record.huazhanArr,e.currentTarget.num);
				lunboimg2.name ="addimg";
				lunboimg2.x = (record._w - lunboimg2.width) / 2 ;
				lunboimg2.y = (record._h - lunboimg2.height ) / 2 - 30 ;
				record.main.addChild(lunboimg2);														
				
			}
		}

	}
	
}
