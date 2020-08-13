package com {
	
	import flash.display.MovieClip;
	import flash.events.*;
	import flash.display.Stage;
	
	
	public class Home extends MovieClip {
		
		private var buttons:MovieClip;
		private var container:*;
		private var stageMc:*;
		private var record:Record;
		private var loadsound:LoadSound;
		
		public function Home(s:Stage) {
			trace("home构造函数执行");
			
			this.gotoAndStop(1);
			
			record = Main.record;
			
			stageMc = s;

			//init();
			
		}
		
		public function init():void{
			
			jinru.addEventListener(MouseEvent.CLICK,jinruClick);			
			shengyin.addEventListener(MouseEvent.CLICK,shengyinClick);			
			
			// 文本框
			txt.width = 375;
			txt.text = record.biaoti;
			
			if(loadsound == null){
				loadsound = new LoadSound();
			}else{
				loadsound.playsound();
			}
			
		}
		
		public function playstate(b:Boolean):void{
			if(b){
				//播放
				loadsound.playvalue();
				this.shengyin.gotoAndStop(1);							

			}else{
				
				//静音
				loadsound.stopvalue();
				this.shengyin.gotoAndStop(2);							
			}
		}
		
		private function jinruClick(e:MouseEvent):void{
			
			playstate(false);
			
			this.gotoAndStop(2);
			
			container = this.parent;
			
			buttons = new Buttons();
			buttons.name ="buttons";
			buttons.x = (record._w - buttons.width) / 2 ;
			buttons.y = record._h - buttons.height - 40;
			container.addChild(buttons);
			
			record.buttons = buttons;
			
		}
		
		private function shengyinClick(e:MouseEvent):void{
			
			if(shengyin.currentFrame == 1){
				loadsound.playvalue();
				
				shengyin.gotoAndStop(2);
				record.playstate = false;
				
				
			}else if(shengyin.currentFrame ==2){
				
				loadsound.playvalue();
				shengyin.gotoAndStop(1);
				record.playstate = true;
				
			}
			
		}
	}
	
}
