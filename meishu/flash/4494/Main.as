package  {
	import flash.display.MovieClip;
	import flash.events.*;
	import com.*;
	
	public class Main extends MovieClip{
		
		public static var record:Record;
		private var imgnum:int;
		private var imglength:int;

		public function Main() {
			// constructor code
			if(stage){
				init();
			}else{
				this.addEventListener(Event.ADDED_TO_STAGE,init);
			}
		}
		
		private function init(e:Event = null):void{
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
			
			
		}
		
	}
	
}
