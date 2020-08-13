package  {
	import flash.display.*;
	import flash.events.*;
	import com.*;
	
	public class Main extends MovieClip{
		
		public static var record:Record;
		//private var loadxml:LoadXML;
		private var imgnum:int;
		private var imglength:int;
		//private var loadimg:LoaderIMG;

		public function Main() {
			// constructor code
			if(stage){
				init();
			}else{
				this.addEventListener(Event.ADDED_TO_STAGE,init);
			}
		}
		
		private function init(e:Event = null):void{
			//stage.scaleMode = StageScaleMode.NO_SCALE;
			//公共记录类
			record = new Record();
			record._h = stage.stageHeight;
			record._w = stage.stageWidth;
			record.main = this;
			
			var datas:Datas = new Datas();
			
			imgnum = 0;
			imglength = 0;
			
			//首页
			var home:Home = new Home(stage);
			this.addChild(home);
			home.init();
			
			record.home = home;						
			
			//stageMask();
			
		}		
		
		var maskmc:MovieClip;
		var loadgame:MovieClip;
		private function stageMask(b:Boolean = true):void{
			 

			if(b){
				
				maskmc = new MovieClip();
				loadgame = new loadingGame();
				
				maskmc.graphics.beginFill(0x000000,0.4);
            	//child.graphics.lineStyle(borderSize, borderColor);
          		maskmc.graphics.drawRect(0, 0, stage.stageWidth, stage.stageHeight);
          		maskmc.graphics.endFill();
         		addChild(maskmc);				
				loadgame.x = (stage.stageWidth - loadgame.width / 2) / 2;
				loadgame.y = (stage.stageHeight - loadgame.height) / 2;
				addChild(loadgame);
				
			}else{
				
				this.removeChild(maskmc);
				this.removeChild(loadgame);
				
			}

		}

	}
	
}
