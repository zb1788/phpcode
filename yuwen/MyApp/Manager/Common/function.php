<?php 
/*定义分组公共函数的地方*/
function clearSQL($str){ 
	$str = str_replace( '"', '\"' ,$str ) ; 
	$str = str_replace( '\\\\', '\\' ,$str ) ; 
	return trim($str);
}
function daddslashes($string, $force = 0, $strip = false) {
	if (!get_magic_quotes_gpc() || $force) {
		if (is_array($string)) {
			// 如果其为一个数组则循环执行此函数
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			// 下面是一个三元操作符，如果$strip为true则执行stripslashes去掉反斜线字符，再执行addslashes
			// 这里为什么要将$string先去掉反斜线再进行转义呢，因为有的时候$string有可能有两个反斜线，stripslashes是将多余的反斜线过滤掉
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
} 

// function copyDir($dirSrc,$dirTo)
// {
//     if(is_file($dirTo))
//     {
//         echo '目标不是目录不能创建！';
//         return;
//     }
//     if(!file_exists($dirTo))
//     {
//         mkdir($dirTo);
//     }
//     $dir_handle = @opendir($dirSrc);
//     if($dir_handle)
//     {
//         while($filename = readdir($dir_handle))
//         {
//             if($filename!="." && $filename!="..")
//             {
//                 $subSrcFile = $dirSrc . "\\".$filename;
//                  $subToFile = $dirTo . "\\".$filename;
                 
//                  if(is_dir($subSrcFile))
//                  {
//                     copyDir($subSrcFile, $subToFile);
//                  }
//                  if(is_file($subSrcFile))
//                  {
//                     copy($subSrcFile, $subToFile);
//                  }
//             }
//         }
//         closedir($dir_handle);
//     }
// }

// function delDir($dirName)
// {
// 	if ($handle = opendir("$dirName"))
// 	{
// 		while ( false !== ($item = readdir( $handle))) 
// 		{
// 			if ( $item != "." && $item != "..") 
// 			{
// 				if ( is_dir( "$dirName/$item")) 
// 				{
// 					delDir("$dirName/$item");
// 				} 
// 				else
// 				{
// 					unlink("$dirName/$item");
// 				}
// 			}
// 		}
// 		closedir($handle);
// 		rmdir($dirName);
// 	}
// }