package com {
	
	public class Datas {
		
		var record:Record;
		
		public function Datas() {
			// constructor code
			record = Main.record;
			
			record.biaoti = "喜洋洋";
			
			
			record.chuangzuoshuoming = "咏鹅，把自己喜欢的画出来。";
			
			//知识
            [Embed(source = "img/57740125e2ca5.jpg")]
            var zhishi1:Class;
            record.zhishiArr.push(zhishi1);


			
			//趣图
            [Embed(source = "img/5774014f72430.jpg")]
            var qvtuimg1:Class;
            record.qvtuArr.push([qvtuimg1,800,600,322,-65,"polygon1",325,140, "[object hexagonMc3]","咏鹅" ]);
            [Embed(source = "img/5774015eabe19.jpg")]
            var qvtuimg2:Class;
            record.qvtuArr.push([qvtuimg2,800,600,422,108,"polygon2",541,140, "[object hexagonMc3]","大公鸡" ]);
            [Embed(source = "img/5774016bf1edc.jpg")]
            var qvtuimg3:Class;
            record.qvtuArr.push([qvtuimg3,800,600,437,330,"polygon3",437,330, "[object hexagonMc3]","小鸭鸭" ]);

	
			
			//画展
            [Embed(source = "img/577401844f8c3.jpg")]
            var huazhan1:Class;
            record.huazhanArr.push([huazhan1,700,700,50,120,"qiqiu1",365,183, "[object balloonMc3]","" ]);
            [Embed(source = "img/5774018ba8b43.jpg")]
            var huazhan2:Class;
            record.huazhanArr.push([huazhan2,700,700,215,236,"qiqiu2",215,236, "[object balloonMc3]","" ]);
            [Embed(source = "img/57740191df221.jpg")]
            var huazhan3:Class;
            record.huazhanArr.push([huazhan3,700,700,420,66,"qiqiu3",572.95,236, "[object balloonMc3]","" ]);

				
			
			//创作
            [Embed(source = "img/577401a831c15.jpg")]
            var chuangzuoimg1:Class;
            record.chuangzuoArr.push([chuangzuoimg1,""]);
            [Embed(source = "img/577401ae5ffbf.jpg")]
            var chuangzuoimg2:Class;
            record.chuangzuoArr.push([chuangzuoimg2,""]);
            [Embed(source = "img/577401b49099e.jpg")]
            var chuangzuoimg3:Class;
            record.chuangzuoArr.push([chuangzuoimg3,""]);
										


			//拓展
            [Embed(source = "img/577401ea96611.jpg")]
            var tuozhan1:Class;
            record.tuozhanArr.push(tuozhan1);
				
		}

	}
	
}
