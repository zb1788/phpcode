package com{
    import flash.display.Sprite;
    import flash.events.*;
    import flash.media.Sound;
    import flash.media.SoundChannel;
    import flash.net.URLRequest;
    import flash.media.*;

    public class LoadSound extends Sprite {
		
        private var url:String;
		private var soundFactory:Sound;
        private var song:SoundChannel;
		private var num:int;
		private var record:Record;

        public function LoadSound() {
			
			record = Main.record;
			
			//url = record.mp3url;
			
//            var request:URLRequest = new URLRequest(url);
//            soundFactory = new Sound();
//            soundFactory.addEventListener(Event.COMPLETE, completeHandler);
//            soundFactory.addEventListener(Event.ID3, id3Handler);
//            soundFactory.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
//            soundFactory.addEventListener(ProgressEvent.PROGRESS, progressHandler);
//            soundFactory.load(request);

			soundFactory =  new mp3class();
            song = soundFactory.play();
			song.addEventListener(Event.SOUND_COMPLETE,soundcomplete);
			num = song.position;
			
        }
		
		public function playvalue():void{
			
   			var transform:SoundTransform=SoundMixer.soundTransform;
			if(transform.volume==0){
				transform.volume=1;
			}else{
				transform.volume=0
			}
   			
   			SoundMixer.soundTransform=transform;
		}
		
		public function stopvalue():void{
			
			var transform:SoundTransform=SoundMixer.soundTransform;
			transform.volume = 0;
			SoundMixer.soundTransform=transform;
			
		}		
		
		public function playsound():void{
			if(song != null){
				song.stop();
				
				 song = soundFactory.play();
			}
		}
		
		private function soundcomplete(e:Event):void{
			
            song = soundFactory.play();
			num = song.position;			
			
		}
		
        private function completeHandler(event:Event):void {
            trace("completeHandler: " + event);
        }

        private function id3Handler(event:Event):void {
            trace("id3Handler: " + event);
        }

        private function ioErrorHandler(event:Event):void {
            trace("ioErrorHandler: " + event);
        }

        private function progressHandler(event:ProgressEvent):void {
            trace("progressHandler: " + event);
        }
    }
}