package com {
	import flash.display.MovieClip;
	
	public class Record {
		public var zhishiArr:Array;
		public var qvtuArr:Array;
		public var huazhanArr:Array;
		public var chuangzuoArr:Array;
		public var tuozhanArr:Array;
		public var main:Main;
		
		public var maskMc:MovieClip;
		
		public var qvtu_huazhan:String;
		
		public var buttons:MovieClip;
		
		public var home:Home;
		
		public var _w:int;
		public var _h:int;
		
		public var biaoti:String="标题";
		public var mp3class:Class;
		
		public var preOBJ:MovieClip;
		
		public var chuangzuoshuoming:String;
		
		public var playstate:Boolean;

		public function Record() {
			// constructor code
			trace("record构造函数执行");
			zhishiArr = new Array();
			qvtuArr = new Array();
			huazhanArr = new Array();
			chuangzuoArr = new Array();
			tuozhanArr = new Array();
			
			playstate = true; //首页音乐  播放状态
			
			_w = 1024;
			_h = 768;
		}

	}
	
}
 