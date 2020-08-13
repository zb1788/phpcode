package com {
	
	public class Datas {
		
		var record:Record;
		
		public function Datas() {
			// constructor code
			record = Main.record;
			
			record.biaoti = "花儿朵朵送老师";
			
			
			record.chuangzuoshuoming = "选自己喜欢的两种以上彩色纸，制作美丽的花朵";
			
			//知识
            [Embed(source = "img/5772a1d24d405.png")]
            var zhishi1:Class;
            record.zhishiArr.push(zhishi1);
            [Embed(source = "img/5772a1dc23437.png")]
            var zhishi2:Class;
            record.zhishiArr.push(zhishi2);


			
			//趣图
            [Embed(source = "img/5772a211bdc75.jpg")]
            var qvtuimg1:Class;
            record.qvtuArr.push([qvtuimg1,800,600,239,93,"polygon1",325,140, "[object hexagonMc5]","满天星" ]);
            [Embed(source = "img/5772a21e205cb.jpg")]
            var qvtuimg2:Class;
            record.qvtuArr.push([qvtuimg2,800,600,444,-68,"polygon2",541,140, "[object hexagonMc5]","玫瑰花" ]);
            [Embed(source = "img/5772a22ad64d1.jpg")]
            var qvtuimg3:Class;
            record.qvtuArr.push([qvtuimg3,800,600,233,73,"polygon3",229,330, "[object hexagonMc5]","向日葵" ]);
            [Embed(source = "img/5772a2392cada.jpg")]
            var qvtuimg4:Class;
            record.qvtuArr.push([qvtuimg4,800,600,405,28,"polygon4",437,330, "[object hexagonMc5]","薰衣草" ]);
            [Embed(source = "img/5772a24ad34d2.jpg")]
            var qvtuimg5:Class;
            record.qvtuArr.push([qvtuimg5,800,600,645,330,"polygon5",645,330, "[object hexagonMc5]","郁金香" ]);

	
			
			//画展
            [Embed(source = "img/5772a323eb02c.jpg")]
            var huazhan1:Class;
            record.huazhanArr.push([huazhan1,800,600,254,-6,"qiqiu1",365,183, "[object balloonMc5]","手工纸花之百合花" ]);
            [Embed(source = "img/5772a32db04cf.jpg")]
            var huazhan2:Class;
            record.huazhanArr.push([huazhan2,800,600,149,6,"qiqiu2",215,236, "[object balloonMc5]","手工纸花之郁金香" ]);
            [Embed(source = "img/5772a33b47283.jpg")]
            var huazhan3:Class;
            record.huazhanArr.push([huazhan3,800,600,530,27,"qiqiu3",572.95,236, "[object balloonMc5]","手工纸花之向日葵" ]);
            [Embed(source = "img/5772a348441b1.jpg")]
            var huazhan4:Class;
            record.huazhanArr.push([huazhan4,800,600,73,25,"qiqiu4",97,250, "[object balloonMc5]","手工纸花之康乃馨" ]);
            [Embed(source = "img/5772a3549a02a.jpg")]
            var huazhan5:Class;
            record.huazhanArr.push([huazhan5,800,600,710,162,"qiqiu5",742,250, "[object balloonMc5]","手工纸花之玫瑰花" ]);

				
			
			//创作
            [Embed(source = "img/5773f2cc15986.JPG")]
            var chuangzuoimg1:Class;
            record.chuangzuoArr.push([chuangzuoimg1,"1.材料准备"]);
            [Embed(source = "img/5773f2d67f5ae.JPG")]
            var chuangzuoimg2:Class;
            record.chuangzuoArr.push([chuangzuoimg2,"2.按照心形轮廓，剪切花瓣颜色的纸张"]);
            [Embed(source = "img/5773f2e00cfc7.JPG")]
            var chuangzuoimg3:Class;
            record.chuangzuoArr.push([chuangzuoimg3,"3.将绿色胶带缠绕到铁丝上"]);
            [Embed(source = "img/5773f2f574988.JPG")]
            var chuangzuoimg4:Class;
            record.chuangzuoArr.push([chuangzuoimg4,"4.用铅笔卷曲花瓣的顶端，制作花瓣形状"]);
            [Embed(source = "img/5773f31548338.JPG")]
            var chuangzuoimg5:Class;
            record.chuangzuoArr.push([chuangzuoimg5,"5.将花瓣包围在铁丝上并用胶带缠绕底部"]);
            [Embed(source = "img/5773f3b0330f6.jpg")]
            var chuangzuoimg6:Class;
            record.chuangzuoArr.push([chuangzuoimg6,"6.在底部加上一些绿色纸张作为叶子"]);
            [Embed(source = "img/5773f33e8109e.jpg")]
            var chuangzuoimg7:Class;
            record.chuangzuoArr.push([chuangzuoimg7,"7.将叶片粘贴在底部"]);
            [Embed(source = "img/5773f3ba1a8a5.jpg")]
            var chuangzuoimg8:Class;
            record.chuangzuoArr.push([chuangzuoimg8,"8.漂亮的玫瑰花就完成了"]);
										
			
		}

	}
	
}
