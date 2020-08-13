<?php
namespace Home\Controller;
use Think\Controller;
/**
 *JpGraph绘图类
 */
class JpgraphController extends CheckController {

    public function index(){
       $this->display();
    }

  /**
     * [index description]
     * [绘制xy曲线图]
     * @return [type]
     */
    public function index1(){
       vendor('jpgraph.jpgraph');
       vendor('jpgraph.jpgraph_line');

       //创建画布,600x400
       $graph = new \Graph(400,300);

       //设置横纵坐标样式
       /*
        lin直线
        text文本
        int整数
        log对数
        */
       //横纵坐标样式:第一个为横坐标，第二个为纵坐标
       //textint:表示横坐标为text，纵坐标为int
       $xyType = 'textint';
       $graph->SetScale($xyType);

       //设置统计图的总标题
       /*
        中文的标题处理方法：$graph->title->Set(iconv("utf-8","gb2312//IGNORE","标题"));
        支持中文
        修改文件jpgraph_ttf.inc.php找到CHINESE_TTF_FONT把他的值改为SIMYOU
        define('CHINESE_TTF_FONT','SIMYOU.TTF');
        $graph->title->SetFont(FF_CHINESE);
        */
       //$graph->title->Set('This is a test');

        $graph->title->SetFont(FF_CHINESE);
        $graph->title->Set(iconv("utf-8","gb2312//IGNORE","标题"));
        $graph->xaxis->title->Set(iconv("utf-8","gb2312//IGNORE","x标题"));
        $graph->yaxis->title->Set(iconv("utf-8","gb2312//IGNORE","y标题"));

        //FF_SIMSUN表示中文简体，对应的字体文件是simsun.ttc，虽然FF_CHINESE和FF_BIG5也表示中文但是它们对应的字体文件是不同的，所以不要弄错。

        $graph->title->SetFont(FF_SIMSUN,FS_BOLD);
        $graph->yaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_SIMSUN,FS_BOLD);


       //得到LinePlot对象
       /**
        * [$lineplot description]
        * @var Y坐标数据必填 array(1=>20, 2=>40),其中key代表x坐标的数，val代表y坐标的数
        * @var X坐标数据选填
        */

       $data = array(0=>20, 1=>30, 2=>40, 3=>50, 4=>12, 5=>38, 6=>55, 7=>120);
       // $data = array(20, 30, 40, 50, 12, 38, 55, 120);
       // $data1 = array('a','b','c','d','e','f','g','h');
       //这两个数组是一个效果

       $lineplot = new \Lineplot($data, $data1);

       //设置图例
       /*
       支持中文，修改文件jpgraph_legend.inc.php
       修改$font_family为FF_CHINESE
        public $font_family=FF_CHINESE,$font_style=FS_NORMAL,$font_size=8;
        */
       $lineplot->Setlegend('图例');

       //将统计图添加到画布
       $graph->Add($lineplot);

       //y 轴连线设定为红色,一定要在添加到画布之后再设置
       $lineplot->SetColor('red');

       //输出图表,
       $graph->Stroke();

       //生成图表图片到本地
       $graph->Stroke('caiji/test.png');
    }

    /**
     * [index2 description]
     * 饼状图
     * @return [type]
     */
    public function index2(){
        vendor('jpgraph.jpgraph');
        vendor('jpgraph.jpgraph_pie');
        //模拟数据
        $data=array(0=>3.5,1=>4.6,2=>9.1,3=>21.9,4=>42.3,5=>90.7,6=>183.5,7=>127.5,8=>61.4,9=>33.5,10=>11.5,11=>4.4);
        //创建画布
        $graph=new \PieGraph(800,500);
        //设置图像边界范围
        $graph->img->SetMargin(30,30,80,30);
        //设置标题
        $graph->title->Set("PiePlot Test");
        //得到饼图对象
        $piePlot=new \PiePlot($data);
        //设置图例
        $piePlot->SetLegends(array(1,2,3,4,5,6,7,8,9,10,11,12));
        //设置图例位置
        $graph->legend->Pos(0.01,0.45,"left","top");
        //添加到画布中
        $graph->Add($piePlot);
        //输出
        $graph->Stroke();
    }


    /**
     * [index3 description]
     * 柱形图
     * @return [type]
     */
    public function index3(){
        vendor('jpgraph.jpgraph');
        vendor('jpgraph.jpgraph_bar');
        $data1y=array(-8,8,9,3,5,6);
        $data2y=array(18,2,1,7,5,4);

        // Create the graph. These two calls are always required
        $graph = new \Graph(400,300);
        $graph->SetScale("textlin");

        $graph->SetShadow();
        $graph->img->SetMargin(40,30,20,40);

        // Create the bar plots
        $b1plot = new \BarPlot($data1y);

        $b1plot->value->Show();
        $b2plot = new \BarPlot($data2y);

        $b2plot->value->Show();

        // Create the grouped bar plot
        $gbplot = new \AccBarPlot(array($b1plot,$b2plot));

        // ...and add it to the graPH
        $graph->Add($gbplot);

        $b1plot->SetFillColor("orange");
        $b2plot->SetFillColor("blue");

        //使用php函数据中文由UTF-8转为GB2312，记住由于iconv本身的一个bug，iconv在转换字符"—"到gb2312时会出错，所以在需要转成的编码后加上 "//IGNORE" 。

        $graph->title->SetFont(FF_CHINESE);

        $graph->title->Set(iconv("utf-8","gb2312//IGNORE","标题"));
        $graph->xaxis->title->Set(iconv("utf-8","gb2312//IGNORE","x标题"));
        $graph->yaxis->title->Set(iconv("utf-8","gb2312//IGNORE","y标题"));

        //FF_SIMSUN表示中文简体，对应的字体文件是simsun.ttc，虽然FF_CHINESE和FF_BIG5也表示中文但是它们对应的字体文件是不同的，所以不要弄错。

        $graph->title->SetFont(FF_SIMSUN,FS_BOLD);
        $graph->yaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_SIMSUN,FS_BOLD);

        // Display the graph
        $graph->Stroke();
    }

    /**
     * [index4 description]
     * XYY坐标图
     * @return [type]
     */
    public function index4(){
        vendor('jpgraph.jpgraph');
        vendor('jpgraph.jpgraph_line');
        $data1 = array(19,23,34,38,45,67,71,78,85,87,90,96); //第一条曲线的数组
        $data2 = array(523,634,371,278,685,587,490,256,398,545,367,577); //第二条曲线的数组
        $graph = new \Graph(400,300); //创建新的Graph对象
        $graph->SetScale("textlin");
        $graph->SetY2Scale("lin");
        $graph->SetShadow(); //设置图像的阴影样式
        $graph->img->SetMargin(40,50,20,70); //设置图像边距
        $graph->title->SetFont(FF_CHINESE);
        $graph->title->Set(iconv("utf-8","gb2312//IGNORE","年度收支表")); //设置图像标题
        $lineplot1=new \LinePlot($data1); //创建设置两条曲线对象
        $lineplot2=new \LinePlot($data2);
        $graph->Add($lineplot1); //将曲线放置到图像上
        $graph->AddY2($lineplot2);
        $graph->xaxis->title->Set("Month"); //设置坐标轴名称
        $graph->yaxis->title->Set("$");
        $graph->y2axis->title->Set("$");
        $graph->title->SetFont(FF_SIMSUN,FS_BOLD); //设置字体
        $graph->yaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
        $graph->y2axis->title->SetFont(FF_SIMSUN,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_SIMSUN,FS_BOLD);
        $lineplot1->SetColor("red"); //设置颜色
        $lineplot2->SetColor("blue");
        $lineplot1->SetLegend("Cost Amount"); //设置图例名称
        $lineplot2->SetLegend("Revenue Amount");
        $graph->legend->SetLayout(LEGEND_HOR); //设置图例样式和位置
        $graph->legend->Pos(0.4,0.95,"center","bottom");
        $graph->Stroke(); //输出图像
    }


    public function index5(){
        vendor('jpgraph.jpgraph');
        vendor('jpgraph.jpgraph_pie');
        vendor('jpgraph.jpgraph_pie3d');
        $data=array(0=>3.5,1=>4.6,2=>9.1,3=>21.9,4=>42.3,5=>90.7,6=>183.5,7=>127.5,8=>61.4,9=>33.5,10=>11.5,11=>4.4);
        //创建画布
        $graph=new \pieGraph(500,500);
        //设置图像边界范围
        $graph->img->SetMargin(30,30,80,30);
        //设置标题
        $graph->title->Set("piePlot3d Test");
        //得到3D饼图对象
        $piePlot3d=new \piePlot3d($data);
        //设置图例
        $piePlot3d->SetLegends(array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"));
        //设置图例位置
        $graph->legend->Pos(0.1,0.15,"left","center");
        //将绘制好的3D饼图加入到画布中
        $graph->Add($piePlot3d);
        //输出
        $graph->Stroke();
    }







}