package com {
	
	import flash.display.*;
	import flash.events.*;
	
	
	public class DaAnJianJie extends MovieClip {
		
		private var daanstr:String;
		
		private var jianjiestr:String;
		
		
		public function DaAnJianJie(s1:String,s2:String) {
			// constructor code
			this.gotoAndStop(1);

			jianjiestr = s1;
			
			daanstr = s2;
			
			if(daanstr != null){
				
				daan.addEventListener(MouseEvent.CLICK,daanClick);
				
			}else{
				
				daan.visible = false;
				
			}
			
			jianjieTxt.text = jianjiestr;
			
			jianjieTxt.mouseEnabled = false;
			
		}
		
		private function daanClick(e:MouseEvent):void{
			
			daan.removeEventListener(MouseEvent.CLICK,daanClick);
			
			this.gotoAndStop(2);
			
			guanbi.addEventListener(MouseEvent.CLICK,guanbiClick);
			
			daanTxt.text = daanstr;
			
			daanTxt.mouseEnabled = false;
			
		}
		
		private function guanbiClick(e:MouseEvent):void{
			
			guanbi.removeEventListener(MouseEvent.CLICK,guanbiClick);
			
			this.gotoAndStop(1);
			
			daan.addEventListener(MouseEvent.CLICK,daanClick);
			
			jianjieTxt.text = jianjiestr;
			
		}
		
	}
	
}
