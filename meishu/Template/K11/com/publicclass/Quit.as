/*
*  退出按钮
*/

package com.publicclass {
	
	import flash.display.*;
	import flash.events.*;
	import flash.system.fscommand;
	import flash.external.ExternalInterface;
	import flash.net.*;
	
	
	public class Quit extends SimpleButton {
		
		
		public function Quit() {
			// constructor code
			//this.gotoAndStop(1);
			//this.buttonMode = true;
			//this.mouseChildren = false;
//			this.addEventListener(MouseEvent.MOUSE_OVER,myOver);
//			this.addEventListener(MouseEvent.MOUSE_OUT,myOut);
			this.addEventListener(MouseEvent.CLICK,myClick);			
			
		}

//		private function myOver(e:MouseEvent):void
//		{
//			this.gotoAndStop(2);
//		}
//
//		private function myOut(e:MouseEvent):void
//		{
//			this.gotoAndStop(1);
//		}

		/*
		*  退出
		*/
		private function myClick(e:MouseEvent):void
		{
			 var isAvailable:Boolean = ExternalInterface.available;
			 if(isAvailable==true){
				 navigateToURL(new URLRequest("javascript:window.opener = null;window.open('','_self');window.close();"),"_self");			
			 }else{
				fscommand("quit"); 
			 }
			
		}
		
	}
	
}
