package com {
	
	public class Datas {
		
		var record:Record;
		
		public function Datas() {
			// constructor code
			record = Main.record;
			
			record.biaoti = "陶泥的世界";
			
			
			record.chuangzuoshuoming = "用普通泥或陶泥制作生活用品。";
			
			//知识
            [Embed(source = "img/5784c0241c62b.png")]
            var zhishi1:Class;
            record.zhishiArr.push(zhishi1);


			
			//趣图
            [Embed(source = "img/5784c0d0387ca.jpg")]
            var qvtuimg1:Class;
            record.qvtuArr.push([qvtuimg1,700,700,115,-29,"polygon1",325,140, "[object hexagonMc5]","1.半山彩陶罐" ]);
            [Embed(source = "img/5784c0dc6181f.jpg")]
            var qvtuimg2:Class;
            record.qvtuArr.push([qvtuimg2,700,700,225,33,"polygon2",541,140, "[object hexagonMc5]","2.彩陶觚" ]);
            [Embed(source = "img/5784c0e5cc720.jpg")]
            var qvtuimg3:Class;
            record.qvtuArr.push([qvtuimg3,700,700,-137,9,"polygon3",229,330, "[object hexagonMc5]","3.彩陶鲵鱼纹瓶" ]);
            [Embed(source = "img/5784c0fb2f535.jpg")]
            var qvtuimg4:Class;
            record.qvtuArr.push([qvtuimg4,700,700,236,214,"polygon4",437,330, "[object hexagonMc5]","4.彩陶人面鱼文图" ]);
            [Embed(source = "img/5784c10483696.jpg")]
            var qvtuimg5:Class;
            record.qvtuArr.push([qvtuimg5,700,700,413,213,"polygon5",645,330, "[object hexagonMc5]","5.陶器" ]);

	
			
			//画展
            [Embed(source = "img/5784c12f48bd1.jpg")]
            var huazhan1:Class;
            record.huazhanArr.push([huazhan1,800,600,249,-10,"qiqiu1",365,183, "[object balloonMc5]","1.陶泥制作的笔筒" ]);
            [Embed(source = "img/5784c142e5183.jpg")]
            var huazhan2:Class;
            record.huazhanArr.push([huazhan2,800,600,147,70,"qiqiu2",215,236, "[object balloonMc5]","2.陶泥制作的花瓶" ]);
            [Embed(source = "img/5784c16a07d28.jpg")]
            var huazhan3:Class;
            record.huazhanArr.push([huazhan3,800,600,269,163,"qiqiu3",572.95,236, "[object balloonMc5]","3.陶泥制作的瓶子" ]);
            [Embed(source = "img/5784c176d49ce.jpg")]
            var huazhan4:Class;
            record.huazhanArr.push([huazhan4,800,600,-142,101,"qiqiu4",97,250, "[object balloonMc5]","4.陶泥制作的罐子" ]);
            [Embed(source = "img/5784c189a34df.jpg")]
            var huazhan5:Class;
            record.huazhanArr.push([huazhan5,800,600,714,70,"qiqiu5",742,250, "[object balloonMc5]","5.陶泥制作的收纳盒" ]);

				
			
			//创作
            [Embed(source = "img/5773ff2a639de.jpg")]
            var chuangzuoimg1:Class;
            record.chuangzuoArr.push([chuangzuoimg1,"1.拿出五分之一的泥在垫板上压扁，做出底。"]);
            [Embed(source = "img/5773ff36f2e61.jpg")]
            var chuangzuoimg2:Class;
            record.chuangzuoArr.push([chuangzuoimg2,"2.搓泥条。"]);
            [Embed(source = "img/5773ff41811e1.jpg")]
            var chuangzuoimg3:Class;
            record.chuangzuoArr.push([chuangzuoimg3,"3.边盘泥条边向下压增加泥条之间的接触面积，使其更牢固。"]);
            [Embed(source = "img/5773ff4f8be3f.jpg")]
            var chuangzuoimg4:Class;
            record.chuangzuoArr.push([chuangzuoimg4,"4.泥底与泥条应该用泥塑刀抹一抹，也是为了更牢固。"]);
            [Embed(source = "img/5773ff63beca2.jpg")]
            var chuangzuoimg5:Class;
            record.chuangzuoArr.push([chuangzuoimg5,"5.里面也抹一下，干了之后才能更加整体成型，不易损坏。"]);
										


			//拓展
            [Embed(source = "img/5784c00bbaad1.png")]
            var tuozhan1:Class;
            record.tuozhanArr.push(tuozhan1);
				
		}

	}
	
}
