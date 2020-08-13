package com {
	
	public class Datas {
		
		var record:Record;
		
		public function Datas() {
			// constructor code
			record = Main.record;
			
			record.biaoti = "sdfs11122";
			
			[Embed(source = "mp3/574bb0f19c459.mp3")]
        	var mp3:Class;
			record.mp3class = mp3;
			
			record.chuangzuoshuoming = "艺术创作d的说明";
			
			//知识
            [Embed(source = "img/576c863fb295d.jpg")]
            var zhishi1:Class;
            record.zhishiArr.push(zhishi1);
            [Embed(source = "img/576c8649449dd.jpg")]
            var zhishi2:Class;
            record.zhishiArr.push(zhishi2);
            [Embed(source = "img/576c864f2e25d.jpg")]
            var zhishi3:Class;
            record.zhishiArr.push(zhishi3);


			
			//趣图
            [Embed(source = "img/576c869af0d7c.jpg")]
            var qvtuimg1:Class;
            record.qvtuArr.push([qvtuimg1,500,700,325,140,"polygon1",325,140, "[object hexagonMc5]"," " ]);
            [Embed(source = "img/576c86a020536.jpg")]
            var qvtuimg2:Class;
            record.qvtuArr.push([qvtuimg2,500,700,541,140,"polygon2",541,140, "[object hexagonMc5]"," " ]);
            [Embed(source = "img/576c86a4156dc.jpg")]
            var qvtuimg3:Class;
            record.qvtuArr.push([qvtuimg3,500,700,229,330,"polygon3",229,330, "[object hexagonMc5]"," " ]);
            [Embed(source = "img/576c86a8acb06.jpg")]
            var qvtuimg4:Class;
            record.qvtuArr.push([qvtuimg4,500,700,437,330,"polygon4",437,330, "[object hexagonMc5]"," " ]);
            [Embed(source = "img/576c86ad37fad.jpg")]
            var qvtuimg5:Class;
            record.qvtuArr.push([qvtuimg5,500,700,645,330,"polygon5",645,330, "[object hexagonMc5]"," " ]);

	
			
			//画展
            [Embed(source = "img/576c86e5c5b1e.jpg")]
            var huazhan1:Class;
            record.huazhanArr.push([huazhan1,500,700,357,-202,"qiqiu1",365,183, "[object balloonMc3]"," " ]);
            [Embed(source = "img/576c86e992be8.jpg")]
            var huazhan2:Class;
            record.huazhanArr.push([huazhan2,500,700,-113,190,"qiqiu2",215,236, "[object balloonMc3]"," " ]);
            [Embed(source = "img/576c86ee14b3c.jpg")]
            var huazhan3:Class;
            record.huazhanArr.push([huazhan3,500,700,582,146,"qiqiu3",572.95,236, "[object balloonMc3]"," " ]);

				
			
			//创作
            [Embed(source = "img/576c86631f4ed.jpg")]
            var chuangzuoimg1:Class;
            record.chuangzuoArr.push([chuangzuoimg1,"说明1"]);
            [Embed(source = "img/576c866896807.jpg")]
            var chuangzuoimg2:Class;
            record.chuangzuoArr.push([chuangzuoimg2,"说明2"]);
            [Embed(source = "img/576c866e2bdc2.jpg")]
            var chuangzuoimg3:Class;
            record.chuangzuoArr.push([chuangzuoimg3,"说明3"]);
            [Embed(source = "img/576c8673b3b04.jpg")]
            var chuangzuoimg4:Class;
            record.chuangzuoArr.push([chuangzuoimg4,"说明4"]);
            [Embed(source = "img/576c867889b96.jpg")]
            var chuangzuoimg5:Class;
            record.chuangzuoArr.push([chuangzuoimg5,"说明5"]);
										


			//拓展
            [Embed(source = "img/576ce922d472e.jpg")]
            var tuozhan1:Class;
            record.tuozhanArr.push(tuozhan1);
            [Embed(source = "img/576ce9272453c.jpg")]
            var tuozhan2:Class;
            record.tuozhanArr.push(tuozhan2);
            [Embed(source = "img/576ce92c6e8a2.jpg")]
            var tuozhan3:Class;
            record.tuozhanArr.push(tuozhan3);
				
		}

	}
	
}
