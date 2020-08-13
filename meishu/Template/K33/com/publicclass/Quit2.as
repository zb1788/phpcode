/*
*  退出按钮
*/

package com.publicclass {
	
	import flash.display.*;
	import flash.events.*;
	import flash.system.fscommand;
	import flash.external.ExternalInterface;
	import flash.net.*;
	
	
	public class Quit2 extends MovieClip {
		
		
		public function Quit2() {
			// constructor code

			this.addEventListener(MouseEvent.CLICK,myClick);			
			
		}

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
