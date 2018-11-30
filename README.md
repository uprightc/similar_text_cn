# similar_text_cn
中文字符串相似度计算类


 *  中文字符串相似度计算类
 *  使用方法：
 *  $str1 = '字符串相似度算法字';
 *  $str2 = '字符串相似度的计算';
 *  $act = new cosine_similar_text_cn(  );
 *  
 *  计算 余弦相似度
 *  $percent = $act->get_similar_percent( $str1, $str2 );
 *  echo "{$str1}\r\n{$str2}\r\ncosine_similar_text_cn 相似度：{$percent}";
 *  
 *  计算 高频子串
 *  $hiFreqWords = $act->get_high_freq_words( $str1, $str2 );
 *  print_r( $hiFreqWords );
 *  

