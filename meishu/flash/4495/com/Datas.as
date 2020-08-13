package com {
	
	public class Datas {
		
		var record:Record;
		
		public function Datas() {
			// constructor code
			record = Main.record;
			
			record.biaoti = "拟人的形象";
			
			
			record.chuangzuoshuoming = "用拟人化的方法，将自己喜欢的形象表现出来。";
			
			//知识
            [Embed(source = "img/5779410be2000.png")]
            var zhishi1:Class;
            record.zhishiArr.push(zhishi1);


			
			//趣图
            [Embed(source = "img/5773a334a78a4.jpg")]
            var qvtuimg1:Class;
            record.qvtuArr.push([qvtuimg1,800,600,239,51,"polygon1",325,140, "[object hexagonMc5]","1.喜洋洋与灰太狼" ]);
            [Embed(source = "img/5773a324bd1c5.jpg")]
            var qvtuimg2:Class;
            record.qvtuArr.push([qvtuimg2,800,600,232,18,"polygon2",541,140, "[object hexagonMc5]","2.皮卡丘" ]);
            [Embed(source = "img/5773a3432c01e.jpg")]
            var qvtuimg3:Class;
            record.qvtuArr.push([qvtuimg3,800,600,206,277,"polygon3",229,330, "[object hexagonMc5]","3.狮子王" ]);
            [Embed(source = "img/5773a3b3882e5.jpg")]
            var qvtuimg4:Class;
            record.qvtuArr.push([qvtuimg4,800,600,255,80,"polygon4",437,330, "[object hexagonMc5]","4.加菲猫" ]);
            [Embed(source = "img/5773a3bca91ca.jpg")]
            var qvtuimg5:Class;
            record.qvtuArr.push([qvtuimg5,800,600,532,153,"polygon5",645,330, "[object hexagonMc5]","5.猫和老鼠" ]);

	
			
			//画展
            [Embed(source = "img/5773a3e78e02e.jpg")]
            var huazhan1:Class;
            record.huazhanArr.push([huazhan1,500,700,299,167,"qiqiu1",365,183, "[object balloonMc5]","1.音乐家小青蛙" ]);
            [Embed(source = "img/5773a3f0af549.jpg")]
            var huazhan2:Class;
            record.huazhanArr.push([huazhan2,500,700,154,186,"qiqiu2",215,236, "[object balloonMc5]","2.拳击运动员虎子" ]);
            [Embed(source = "img/5773a3fa374a0.jpg")]
            var huazhan3:Class;
            record.huazhanArr.push([huazhan3,500,700,550,123,"qiqiu3",572.95,236, "[object balloonMc5]","3.跳高运动员熊熊" ]);
            [Embed(source = "img/5773a433e326f.jpg")]
            var huazhan4:Class;
            record.huazhanArr.push([huazhan4,500,700,-114,211,"qiqiu4",97,250, "[object balloonMc5]","4.学霸牛牛" ]);
            [Embed(source = "img/5773a41dde687.jpg")]
            var huazhan5:Class;
            record.huazhanArr.push([huazhan5,500,700,530,208,"qiqiu5",742,250, "[object balloonMc5]","5.时装模特虎妞" ]);

				
			
			//创作
            [Embed(source = "img/5773f698c423f.jpg")]
            var chuangzuoimg1:Class;
            record.chuangzuoArr.push([chuangzuoimg1,"1.画出凯蒂猫的头部轮廓。"]);
            [Embed(source = "img/5773a46e1477b.jpg")]
            var chuangzuoimg2:Class;
            record.chuangzuoArr.push([chuangzuoimg2,"2.添画完成头部细节。"]);
            [Embed(source = "img/5773a477f302c.jpg")]
            var chuangzuoimg3:Class;
            record.chuangzuoArr.push([chuangzuoimg3,"3.画出身体轮廓，添画细节，调整完成。"]);
										


			//拓展
            [Embed(source = "img/5779411ad71c7.png")]
            var tuozhan1:Class;
            record.tuozhanArr.push(tuozhan1);
				
		}

	}
	
}
