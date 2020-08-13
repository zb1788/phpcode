package com {
	
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.text.*;
	
	
	public class Buttons extends MovieClip {
		
		private var btnMc:MovieClip;
		private var prebtnMc:MovieClip;
		private var record:Record;
		private var container:Object;
		
		//四个影片背景
		
		public function Buttons() {
			// constructor code
			init();
			record = Main.record;
			
			container = record.main;
			
			addzhishi();
			
		}
		
		private function init():void{
			//所有按钮添加状态
			for(var i:int = this.numChildren - 1; i >= 0 ;i --){
				trace(this.getChildAt(i).name);
				buttonstate(this.getChildAt(i) as MovieClip);
			}
			zhishichuang.gotoAndStop(2);
			prebtnMc = zhishichuang;
			 
		}
		
		//子按钮状态
		private function buttonstate(m:MovieClip):void{
			btnMc = m;
			btnMc.gotoAndStop(1);
			btnMc.buttonMode = true;
			btnMc.addEventListener(MouseEvent.CLICK,btnClick); 
			//btnMc.addEventListener(MouseEvent.MOUSE_OVER,btnOver);
			//btnMc.addEventListener(MouseEvent.MOUSE_OUT,btnOut);
		}
		
		private function btnClick(e:MouseEvent):void{

			var obj:MovieClip = e.currentTarget as MovieClip;
			switch(obj.name){ 
				case "zhishichuang":
				clickState(obj)?trace("知识窗图片轮播"):clearstage();
				prebtnMc = zhishichuang;
				addzhishi();
				break;
				
				case "qvtuyuan": 
				clickState(obj)?trace("趣图园图片轮播"):clearstage();
				prebtnMc = qvtuyuan;
				addqvtu();
				record.qvtu_huazhan = "趣图";
				break;				
				
				case "xiaoxiaohuazhan":
				clickState(obj)?trace("小小画展图片轮播"):clearstage();
				prebtnMc = xiaoxiaohuazhan;
				addhuazhan();
				record.qvtu_huazhan = "画展";
				break;				
				
				case "yishuchuangzuo":
				clickState(obj)?trace("艺术创作图片轮播"):clearstage();
				prebtnMc = yishuchuangzuo;
				addchuangzuo();
				break;				
				
				case "tuozhan":
				clickState(obj)?trace("拓展图片轮播"):clearstage();
				prebtnMc = tuozhan;
				addtuozhan();
				break;				
				
				default:
				trace("没有这个按钮名称对应的单击事件");
								
			}
			
		}
		 
		private function btnOver(e:MouseEvent):void{
			//btnMc.gotoAndStop(2);
		}
		
		private function btnOut(e:MouseEvent):void{
			//btnMc.gotoAndStop(1);
		}
		
		//按钮单击过后的状态
		private function clickState(m:MovieClip):Boolean{
			
			if(m != prebtnMc){
				clearstage();
				prebtnMc.gotoAndStop(1);
			}else{
				return false;
			}
			
			if(m.currentFrame == 1){
				m.gotoAndStop(2);
				return true;
			}else if(m.currentFrame==2){
				m.gotoAndStop(1);
				return false;
			}
			//prebtnMc.gotoAndStop(1);
			return false;
		}
		
		//知识窗 
		private function addzhishi():void{
			
			var lunboimg:LunboIMG2 = new LunboIMG2(record.zhishiArr);
			lunboimg.name ="addimg";
			lunboimg.x = (record._w - lunboimg.width) / 2 ;
			lunboimg.y = (record._h - lunboimg.height ) / 2 - 30 ;
			container.addChild(lunboimg);						
			
		}
		//趣图
		private function addqvtu():void{
			
			var qvtu:QvTu = new QvTu(record.qvtuArr);
		}
		//画展
		private function addhuazhan():void{
			var qvtu:QvTu = new QvTu(record.huazhanArr);
		}
		//创作
		private var txtMc:MovieClip;
		private function addchuangzuo():void{

            var imgslider:MovieClip = new ImgSlider();
			imgslider.init(record.chuangzuoArr);
			record.main.addChild(imgslider);
			imgslider.name ="addimg";
			imgslider.x = (record._w - imgslider.width) / 2  + 46;
			imgslider.y = 265 ;				
			
			//chuangzuo.gundong();
			
			if(record.chuangzuoshuoming != ""){
				txtMc = new MovieClip();
				
				var shu:MovieClip = new shubi();
				txtMc.addChild(shu);				
				
	            var label:TextField = new TextField();
	            label.autoSize = TextFieldAutoSize.LEFT;
	          //  label.background = true;
	           // label.border = true;

	            var format:TextFormat = new TextFormat();
	            format.font = "微软雅黑";
	            format.color = 0x000000;
	            format.size = 30;
	          //  format.underline = true;

	            label.defaultTextFormat = format;
				label.x = 55;
				label.width = 650;
				label.wordWrap = true;
				label.text = record.chuangzuoshuoming;
				txtMc.addChild(label);
				txtMc.name = "txt";
				txtMc.x = imgslider.x + 50;
				txtMc.y = 140;
				
	            record.main.addChild(txtMc);														
				
			}

			
		}
		
		//拓展
		private function addtuozhan():void{
			
			var lunboimg:LunboIMG2 = new LunboIMG2(record.tuozhanArr);
			lunboimg.name ="addimg";
			lunboimg.x = (record._w - lunboimg.width) / 2 ;
			lunboimg.y = (record._h - lunboimg.height ) / 2 - 30 ;
			container.addChild(lunboimg);						
			
		}		
		//清除舞台
		public function clearstage():void{
			for(var j:int = 0; j<record.main.numChildren;j++){
				var _obj2:* = record.main.getChildAt(j);

				if(_obj2.name=="addimg"){
					record.main.removeChild(_obj2);
				}
				if(_obj2.name == "img"){
					record.main.removeChild(_obj2);
					record.maskMc = null;
				}
				if(txtMc != null){
					record.main.removeChild(txtMc);
					txtMc = null;
				}				
				
				
			}
		}
	}
	
}
