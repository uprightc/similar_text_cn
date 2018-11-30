# similar_text_cn
中文字符串相似度计算类

使用方法：<hr>
 $str1 = '字符串相似度算法字';<br>
 $str2 = '字符串相似度的计算';<br>
 $act = new cosine_similar_text_cn(  );<br>
<hr>
计算 余弦相似度<br>
 $percent = $act->get_similar_percent( $str1, $str2 );<br>
 echo "{$str1}\r\n{$str2}\r\ncosine_similar_text_cn 相似度：{$percent}";<br>
<hr>
计算 高频子串<br>
 $hiFreqWords = $act->get_high_freq_words( $str1, $str2 );<br>
 print_r( $hiFreqWords );<br>
 
