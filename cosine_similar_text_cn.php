<?php

/**
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
 */
 
class cosine_similar_text_cn{
	function __construct( $maxWordLen=2, $encoding='GB2312' ){
		$this->maxWordLen = $maxWordLen;	# 最大匹配子串长度
		$this->encoding   = $encoding;		# 字符串编码
	}
	
	# 为计算余弦相似度 分割 最小匹配子串
	private function strSplit2Words( $str ){
		$strLength = mb_strlen( $str , $this->encoding );
		$ret = [];
		# 中文的词组匹配，两个字以上才有意义
		for( $i=2; $i <= $this->maxWordLen; $i++ ){
			$arrKey = "L{$i}";
			$ret[ $arrKey ] = [];
			for( $j=0; $j<$strLength; $j++ ){
				$word = mb_substr( $str, $j, $i, $this->encoding );
				$cnWord = $word; 
				if ( $i == mb_strlen( $word , $this->encoding ) ){
					# 数字串不分段
					if ( !is_numeric( $word ) ){
						$ret[ $arrKey ][ $j % $i ][] = $word;
					}
				}
			}
		}
		# 数字串不分段，单独列入 子串 数组
		if ( preg_match('/[0-9]{2,}/', $str, $matches ) ){
			foreach( $matches as $word ){
				$wordLen = mb_strlen( $word , $this->encoding );
				$arrKey = "L{$wordLen}";
				$ret[ $arrKey ]['numeric'][] = $word;
			}
		}
		return $ret;
	}
	
	# 计算两个字符串的余弦相似度
	function get_similar_percent( $str1, $str2 ){
		$bow1 = $this->strSplit2Words( $str1 );
		$bow2 = $this->strSplit2Words( $str2 );
		#print_r( $bow1 );
		#print_r( $bow2 );
		$bow = [];
		foreach ( $bow1 as $wordLen => $wordSet ){
			foreach( $wordSet as $wordList ){
				foreach( $wordList as $word ){
					#var_dump( $word );echo PHP_EOL;
					if ( !isset( $bow[ $word ] ) ){
						$bow[ $word ] = ['A'=>1, 'B'=>0];
					}
					else{
						$bow[ $word ]['A'] += 1;
					}
				}
			}
		}
		foreach ( $bow2 as $wordLen => $wordSet ){
			foreach( $wordSet as $wordList ){
				foreach( $wordList as $word ){
					#var_dump( $word );echo PHP_EOL;
					if ( !isset( $bow[ $word ] ) ){
						$bow[ $word ] = ['A'=>0,'B'=>1];
					}
					else{
						$bow[ $word ]['B'] += 1;
					}
				}
			}
		}
		
		$x  = 0; 
		$y1 = 0; 
		$y2 = 0;
		foreach( $bow as $str => $v ){
			$x  += $v['A'] * $v['B'];
			$y1 += $v['A'] * $v['A'];
			$y2 += $v['B'] * $v['B'];
		}
		
		return ( $y1==0 || $y2==0 ) ? -1 : ( $x / ( sqrt($y1) * sqrt($y2) ) );
	}
	
	# 按字符分析 两个字符串间的 高频子串
	function get_high_freq_words( $str1, $str2 ){
		$matrix = [];
		$charList1 = [];		$charList2 = [];

		$str1Length = mb_strlen( $str1 , $this->encoding );
		$str2Length = mb_strlen( $str2 , $this->encoding );
		$strA = $str1;	$strB = $str2;
		if ( $str1Length < $str2Length ){
			$strA = $str2;	$strB = $str1;
		}
		
		# 遍历 分割 较短的 字符串
		$str1Length = mb_strlen( $str1 , $this->encoding );
		for( $i=0; $i<$str1Length; $i++ ){
			$char = mb_substr( $str1, $i, 1, $this->encoding );
			$charList1[] = $char;
		}
		# 遍历 分割 较长的 字符串，同时生成 关联关系矩阵
		$str2Length = mb_strlen( $str2 , $this->encoding );
		for( $j=0; $j<$str2Length; $j++ ){
			$char = mb_substr( $str2, $j, 1, $this->encoding );
			$i = 0;
			foreach( $charList1 as $char1  ){
				if ( $char1 == $char ){
					$matrix[$i][$j] = 1;
				}
				else{
					$matrix[$i][$j] = 0;
				}
				$i++;				
			}
		}

		# 检查矩阵中所有匹配的首字符坐标
		$wordHeadCharPos = [];
		for( $i=0; $i<$str1Length; $i++ ){
			for( $j=0; $j<$str2Length; $j++ ){
				if ( $matrix[$i][$j]==1 ){
					if ( $i==0 || $j==0 ){
						$wordHeadCharPos[] = [$i,$j];
					}
					else{
						if ( $matrix[($i-1)][($j-1)]==0 ){
							$wordHeadCharPos[] = [$i,$j];
						}
					}
				}
			}
		}

		# 检索 所有 共有的子字符串
		$bothWords = [];
		foreach( $wordHeadCharPos as $pos ){
			$i = $pos[0];	$j = $pos[1];
			$bothWord = '';
			while( $matrix[$i][$j] == 1 ){
				$bothWord .= $charList1[$i];
				$i++;	$j++;
			}
			$bothWords[] = $bothWord;
		}
		$this->hiFreqWords = array_unique( $bothWords );
		return $this->hiFreqWords;
	}
	
}
?>
