package com
{
	import flash.display.*;
	import flash.events.*;
	import flash.filters.BitmapFilter;
	import flash.filters.BitmapFilterQuality;
	import flash.filters.GlowFilter;
	public class XuanZeState extends MovieClip
	{
		private var record:Record;

		public function XuanZeState()
		{
			// constructor code
			//record = Main.record;
			//record.preOBJ = null;

		}

		public function GlowFilterExample(m:MovieClip)
		{
				var filter:BitmapFilter = getBitmapFilter();
				var myFilters:Array = new Array();
				myFilters.push(filter);
				m.filters = myFilters;

		}

		private function getBitmapFilter():BitmapFilter
		{
			var color:Number = 0x41220E;
			var alpha:Number = 0.8;
			var blurX:Number = 4;
			var blurY:Number = 4;
			var strength:Number = 10;
			var inner:Boolean = false;
			var knockout:Boolean = false;
			var quality:Number = BitmapFilterQuality.HIGH;

			return new GlowFilter(color,
			                                  alpha,
			                                  blurX,
			                                  blurY,
			                                  strength,
			                                  quality,
			                                  inner,
			                                  knockout);
		}

	}

}