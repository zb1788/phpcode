package  {
	import flash.display.*;
	import flash.events.*;
	import com.*;
	import com.btn.*;
	
	public class Main extends MovieClip{
		
		public static var record:Record = new Record();
		
		public var currNum:int;						//当前图片播放到的位置
		
		private var mainstage:MovieClip = new MainStage();				//主影片
		
		private var datas:Datas = new Datas();			//初始化数据
		
		private var contentMc:MovieClip = new MovieClip();	// 图片 文字 的容器
		
		private var currBtn:MovieClip;					//当前按下的按钮
		
		private var preBtn:MovieClip;					//记录上一次按下的按钮
		
		private var leftarrow:MovieClip = new LeftArrow();		//向左按钮
			
		private var rightarrow:MovieClip = new RightArrow();	//向右按钮
		
		private var functionMc:MovieClip;				//实例化对应的按钮功能类
		
		private var currArr:Array;						//当前用到的数组
		
		private var loadsound:LoadSound;				//声音播放

		public function Main() {

			if(stage){
				init();
			}else{
				this.addEventListener(Event.ADDED_TO_STAGE,init);
			}
		}
		
		/*
		* 程序入口
		*/
		public function init(e:Event = null):void{
			
			//初始化舞台内容			
			mainstage.gotoAndStop(1);
			
			this.addChild(mainstage);
			
			mainstage.txt.text = record.biaoti;
			
			//进入按钮添加侦听
			mainstage.jinru.addEventListener(MouseEvent.CLICK,jinruClick);
			
			//容器加入舞台
			this.addChild(contentMc);
			
			contentMc.x = 107;
			
			contentMc.y = 90;
			
			contentMc.graphics.beginFill(0x000000,0);		//容器为透明
			
			contentMc.graphics.drawRect(0,0,810,571);
			
			contentMc.graphics.endFill();
			
			//左右按钮 初始
			this.addChild(leftarrow);
			
			leftarrow.x = 110;
			
			leftarrow.name ="leftarrow";

			this.addChild(rightarrow);
			
			rightarrow.name = "rightarrow";
			
			rightarrow.x = stage.stageWidth - 110 - rightarrow.width;
			
			leftarrow.y = rightarrow.y = 405;
			
			rightarrow.visible = leftarrow.visible = false;
			
			leftarrow.addEventListener(MouseEvent.CLICK,arrowClick);
			
			rightarrow.addEventListener(MouseEvent.CLICK,arrowClick);
			
			//声音
			mainstage.shengyin.addEventListener(MouseEvent.CLICK,shengyinClick);			

			if(loadsound == null){
				
				loadsound = new LoadSound();
				
			}else{
				
				loadsound.playsound();
				
			}
			
		}
		
		/*
		* 声音播放
		*/		
		private function shengyinClick(e:MouseEvent):void{
			
			if(mainstage.shengyin.currentFrame == 1){
				
				loadsound.playvalue();
				
				mainstage.shengyin.gotoAndStop(2);				
				
				record.playstate = false;
				
			}else if(mainstage.shengyin.currentFrame ==2){
				
				loadsound.playvalue();
				
				mainstage.shengyin.gotoAndStop(1);
				
				record.playstate = true;

			}
			
		}		
		
		/*
		* 外部控制声音播放
		*/
		public function playstate(b:Boolean):void{
			
			if(b){
				//播放
				loadsound.playvalue();
				mainstage.shengyin.gotoAndStop(1);							

			}else{
				
				//静音
				loadsound.stopvalue();
				mainstage.shengyin.gotoAndStop(2);							
			}
		}		
		
		/*
		* 进入影片第二帧
		*/
		private function jinruClick(e:MouseEvent):void{
			
			playstate(false);
			
			mainstage.gotoAndStop(2);
			
			//给 菜单按钮添加侦听
			addEvent(mainstage.zhishichuang);
			
			addEvent(mainstage.qvtuyuan);
			
			addEvent(mainstage.xiaohuazhan);
			
			addEvent(mainstage.yishuchuangzuo);
			
			addEvent(mainstage.tuozhan);
			
			//初始化到知识窗
			mainstage.zhishichuang.gotoAndStop(2);
			
			preBtn = mainstage.zhishichuang;			
			
			currBtn = mainstage.zhishichuang;
			
			functionMc = new ZhiShiChuang();
					
			currArr = record.zhishiArr;
					
			functionMc.init(currArr,contentMc);
					
			//方向箭头
			directionBtn(currNum,currArr.length);
			
		}
		
		/*
		* 菜单 按钮添加侦听
		*/
		private function addEvent(_btn:MovieClip):void{
			
			_btn.gotoAndStop(1);
			
			_btn.addEventListener(MouseEvent.CLICK,btnClick);

		}
		
		/*
		* 菜单按钮调用各自功能类
		*/
		private function btnClick(e:MouseEvent):void{
			
			currBtn = e.currentTarget as MovieClip;
			
			directionBtn(0,0);	//初始 左右按钮
			
			preBtn.gotoAndStop(1);
			
			currBtn.gotoAndStop(2);
			
			currBtn.btn.buttonMode = true;
			
			clearStage();			//清除 内容容器 重新加载 新内容
			
			currNum = 0;
			
			switch(currBtn.name){
				
				case "zhishichuang":
				
					functionMc = new ZhiShiChuang();
					
					currArr = record.zhishiArr;
					
					functionMc.init(currArr,contentMc);
					
					//方向箭头
					directionBtn(currNum,currArr.length);
				break;
				
				case "qvtuyuan":
				
					functionMc = new QvTuYuan();
					
					currArr = record.qvtuArr;
					
					functionMc.init(currArr,contentMc);
				
				break;				
				
				case "xiaohuazhan":
				
					functionMc = new HuaZhan();
					
					currArr = record.huazhanArr;
					
					functionMc.init(currArr,contentMc);				
				
				break;
				
				case "yishuchuangzuo":
				
					functionMc = new ChuangZuo();
					
					currArr = record.chuangzuoArr;
					
					functionMc.init(currArr,contentMc);								
					
					//方向箭头
					//directionBtn(currNum,currArr.length);					
				
				break;				
				
				case "tuozhan":
				
					functionMc = new TuoZhan();
					
					currArr = record.tuozhanArr;
					
					functionMc.init(currArr,contentMc);
					
					//方向箭头
					directionBtn(currNum,currArr.length);
				break;				
				
				
				default:
				trace("菜单中没有这个按钮！ 请核实代码！");
				
			}
			
			preBtn = currBtn;
		}
		
		/*
		* 方向箭头
		* 当前位置 ， 总数量
		*/
		public function directionBtn(curr:int,total:int):void{
			
			if(total <= 0){
				
				rightarrow.visible = false;
				
				leftarrow.visible = false;
				
				return;
			}
			
			if(curr > 0){
				
				leftarrow.visible = true;
				
			}else{
				
				leftarrow.visible = false;
			}
			
			if(curr < total - 1){
				
				rightarrow.visible = true;
				
			}else{
				
				rightarrow.visible = false;
			}
			
		}
		
		/*
		* 方向按钮的单击
		*/
		private function arrowClick(e:MouseEvent):void{
			
			clearStage();
			
			var mc:MovieClip = e.currentTarget as MovieClip;
			
			if(mc.name == "leftarrow"){
				
				currNum --;
				
				functionMc.init(currArr,contentMc,currNum,currBtn.name);
					
				//方向箭头
				directionBtn(currNum,currArr.length);				
				
			}else{
				
				currNum ++;
				
				functionMc.init(currArr,contentMc,currNum,currBtn.name);
					
				//方向箭头
				directionBtn(currNum,currArr.length);								
				
			}
			
		}
		
		/*
		* 清理舞台
		*/
		public function clearStage(s:String = null):void{

			while(contentMc.numChildren > 0){
				
				contentMc.removeChildAt(0);

			}
			var left:MovieClip = this.getChildByName("leftarrow1") as MovieClip;
			
			var right:MovieClip = this.getChildByName("rightarrow1") as MovieClip;
			
			if(left != null){
				
				this.removeChild(left);
				
			}
			
			if(right != null){
				
				this.removeChild(right);
				
			}
			
			if(s=="clear"){
				
				this.removeChild(contentMc);
				
				contentMc = null;
				
				left = this.getChildByName("leftarrow") as MovieClip;
			
				right = this.getChildByName("rightarrow") as MovieClip;
			
				if(left != null){
				
					this.removeChild(left);
					
					left = null;
				
				}
			
				if(right != null){
				
					this.removeChild(right);
					
					right = null;
				
				}				
				
				if(mainstage != null){
					
					this.removeChild(mainstage);
					
					mainstage = null;
					
				}
				
				
			}

		}

	}
	
}
