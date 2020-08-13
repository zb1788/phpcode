package com.publicclass {
	
	import flash.display.SimpleButton;
	import com.*;
	import flash.events.MouseEvent;
	import flash.display.MovieClip;
	
	
	public class ShouYe extends SimpleButton {
		
		private var record:Record;
		
		
		public function ShouYe() {
			// constructor code
			
			record = Main.record;
			this.addEventListener(MouseEvent.CLICK,myClick);
		}
		
		private function myClick(e:MouseEvent):void{
			record.buttons.clearstage();
			
			for(var i:int = 0;i < record.main.numChildren; i++){
				var obj:MovieClip = record.main.getChildAt(i) as MovieClip;
				if(obj != null){
					
					if(obj.name == "buttons"){
						record.main.removeChild(obj);
					}					
					
				}

			}
			
			record.home.gotoAndStop(1);
			record.home.init();
			record.home.playstate(record.playstate);
			
		}
	}
	
}
