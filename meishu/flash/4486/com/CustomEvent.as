package com{

    import flash.events.Event;
    import flash.display.BitmapData;

    //导入事件类

    public class CustomEvent extends Event {

        //声明自定义事件扩展自事件类成为其子类

        public static  const SENDFLOWER:String="sendFlower";

        //声明静态常量作为事件类型1

        public static  const SENDCAR:String="sendCar";

        //声明静态常量作为事件类型2

        public var _info:*;

        //声明变量储存事件信息，这也是我们用自定义事件的主要原因，可以用他来

        //携带额外的信息

        public function CustomEvent(type:String,inf:*=null) {

            super(type);

            //调用父类构造函数并设置传入的参数作为事件类型

            _info=inf;

            //将传入的参数2存入info

        }

    }

}
