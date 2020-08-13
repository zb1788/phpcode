<?php
function base64EncodeImage ($image_file) {
    $base64_image = '';
    $image_info = getimagesize($image_file);
    $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
    $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    return $base64_image;
  }

  function split_str($string, $type='array', $charset='utf-8'){
    //通过ord()函数获取字符的ASCII码值，如果返回值大于 127则表示为中文字符的一半，再获取后一半组合成一个完整字符
    $flag = false;
    if(strtolower($charset) == 'utf-8'){
        //如果utf-8环境
        $flag = true;
		$tmp = $string;
        $string = iconv('utf-8', 'gbk', $string);//由于ord函数在gbk下单个中文长度为2，utf-8下长度为3
		if(!$string){
			$string = $tmp;
			$flag = false;
		}
    }

    if(strtolower($charset) != 'gbk' && strtolower($charset) != 'utf-8')
    {
        exit('参数不合法!');
    }

    //把字符串转化为ascii码存入数组,如果是中文是由两个ASCII码组成，英文是一个
    $length = strlen($string);

    $result = array();
    for($i=0; $i<$length; $i++){
        if(ord($string[$i])>127){
            $result[] = ord($string[$i]).' '.ord($string[++$i]);
        }else{
            $result[] = ord($string[$i]);
        }
    }
    if(strtolower($type) == 'array'){
        //如果返回值要数组
        $str = '';
        foreach($result as $v){
            $isEmpty = strstr($v,' ');
            if(empty($isEmpty)){
                $tmpstr = chr($v);
                if($flag){
                    $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
                }
                $data[] = $tmpstr;
            }else{
                list($a,$b) = explode(' ',$v);
                $tmpstr = chr($a).chr($b);
                if($flag){
                    $tmpstr = iconv('gbk', 'utf-8', $tmpstr);
                }
                $data[] = $tmpstr;
            }
        }
        return $data;
    }elseif(strtolower($type) == 'string'){
        $data = array();
        foreach($result as $v){
            $isEmpty = strstr($v,' ');
            if(empty($isEmpty)){
                $str .= chr($v);
            }else{
                list($a,$b) = explode(' ',$v);
                $str .= chr($a).chr($b);
            }
        }
        $str=iconv('gbk', 'utf-8', $str);
        return $str;
    }else{
        exit('参数不合法！');
    }
}
