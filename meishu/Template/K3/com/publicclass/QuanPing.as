package com.publicclass {
	import flash.display.*;
	import flash.events.*;
	
	public class QuanPing extends  MovieClip{

		public function QuanPing() {
			// constructor code			
			this.addEventListener(MouseEvent.CLICK,myClick);
			this.addEventListener(Event.ENTER_FRAME,loop);
		}
		
		private function myClick(e:MouseEvent):void{
			if(stage.displayState == StageDisplayState.NORMAL){
				this.gotoAndStop(2);
				stage.displayState= StageDisplayState.FULL_SCREEN;
			}else{
				this.gotoAndStop(1);
				stage.displayState = StageDisplayState.NORMAL;
			}
		}
		
		private function loop(e:Event):void{
			if(this.parent != null){
				
				if(stage.displayState == StageDisplayState.FULL_SCREEN){
					this.gotoAndStop(2);
				}else{
					this.gotoAndStop(1);
				}							
				
				this.removeEventListener(Event.ENTER_FRAME,loop);
				
			}

		}

	}
	
}
