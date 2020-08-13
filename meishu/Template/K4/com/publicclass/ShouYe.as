package com.publicclass {
	
	import flash.display.SimpleButton;
	import com.*;
	import flash.events.MouseEvent;
	import flash.display.MovieClip;
	
	
	public class ShouYe extends SimpleButton {
		
		private var record:Record;
		
		
		public function ShouYe() {
			// constructor code
			
			this.addEventListener(MouseEvent.CLICK,myClick);
		}
		
		private function myClick(e:MouseEvent):void{
			
			var main:MovieClip = MovieClip(this.parent.parent);
			
			main.clearStage();
			
			MovieClip(this.parent).gotoAndStop(1);
			
			main.init();
			
			main.playstate(Main.record.playstate);
			
		}
	}
	
}
