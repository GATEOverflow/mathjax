<?php
function qa_string_to_words1($string, $tolowercase = true, $delimiters = false, $splitideographs = true, $splithyphens = true)
{
	if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }

	global $qa_utf8punctuation;

	if ($tolowercase)
		$string = qa_strtolower($string);

	$string = strtr($string, $qa_utf8punctuation);

	$separator = QA_PREG_INDEX_WORD_SEPARATOR;
	if ($splithyphens)
		$separator .= '|\-';
	$separator .= '|\$';
	if ($delimiters) {
		if ($splitideographs)
			$separator .= '|' . QA_PREG_CJK_IDEOGRAPHS_UTF8;

	} else {
		$string = preg_replace("/(\S)'(\S)/", '\1\2', $string); // remove apostrophes in words

		if ($splitideographs) // put spaces around CJK ideographs so they're treated as separate words
			$string = preg_replace('/' . QA_PREG_CJK_IDEOGRAPHS_UTF8 . '/', ' \0 ', $string);
	}

	return preg_split('/(' . $separator . '+)/', $string, -1, PREG_SPLIT_NO_EMPTY | ($delimiters ? PREG_SPLIT_DELIM_CAPTURE : 0));
}
function qa_shorten_string_line($string,$length=0, $ellipsis = ' ... ')
{
	global $counter;
	$string = strtr($string, "\r\n\t", '   ');
	//return $string;
	$string = str_replace("$$", "$", $string);
	if (qa_strlen($string) > $length) {
		$remaining = $length - qa_strlen($ellipsis);

		$words = qa_string_to_words1($string, false, true);
		$stringwords = count($words);
		$prefix = '';
		$suffix = '';
		$prefixdollar = 0; $suffixdollar = 0;

		$sslashon = $stexton = $stextmode = false;
		$pslashon = $ptexton = $ptextmode = false;
		for ($addword = 0; $addword < $stringwords; $addword++) {
			$tosuffix = $addword % 3 == 1; // order: prefix, suffix, prefix, prefix, suffix, prefix, ...

			$word = $tosuffix ? array_pop($words) : array_shift($words);

			$wordLength = qa_strlen($word);
			if(($word == '$') && ($wordLength <= $remaining))
			{
				if($tosuffix && !$stextmode)
				{
					if($suffixdollar == 0)$suffixtemp = $suffix;
					$suffixdollar++;
					if(($suffixdollar%2) == 0)
					{
						$suffixtemp = $word. $suffix;
					}

				}
				else if(!$ptextmode)
				{
					if($prefixdollar == 0)$prefixtemp = $prefix;
					$prefixdollar++;
					if(($prefixdollar%2) == 0)
					{
						$prefixtemp = $prefix.$word;
					}
				}
			}
			if($tosuffix) {
				if($suffixdollar%2) {
					if($stextmode && $word == "}") {
						$stextmode = false;
					}
					if($stexton && trim($word)) {
						if($word == "{") {
							$stextmode = true;
						}
						else {
							$stextmode = false;
						}
						$stexton = false;
					}
					if($sslashon && trim($word)) {
						if( $word == "text") {
							$stexton = true;
						}
						else  {
							$sslashon = false;
							$stexton = false;
						}
						$sslason = false;
					}
					if($sslashon && $word == "text") {
						$stexton = true;
						$sslashon = false;
					}
					if ($word == "\\") {
						$sslashon = true;
					}
				}
			}
			else {
				if($prefixdollar%2) {
					if($ptextmode && $word == "}") {
						$ptextmode = false;
					}
					if($ptexton && trim($word)) {
						if($word == "{") {
							$ptextmode = true;
						}
						else {
							$ptextmode = false;
						}
						$ptexton = false;
					}
					if($pslashon && trim($word)) {
						if( $word == "text") {
							$ptexton = true;
						}
						else {
							$ptexton = false;
						}
						$pslason = false;
					}
					if($pslashon && $word == "text") {
						$ptexton = true;
						$pslashon = false;
					}
					if ($word == "\\") {
						$pslashon = true;
					}
				}
			}

			if ($wordLength > $remaining)
				break;

			if ($tosuffix)
				$suffix = $word . $suffix;
			else
				$prefix .= $word;

			$remaining -= $wordLength;
		}
		if(($suffixdollar%2) == 1) $suffix = $suffixtemp;
		if(($prefixdollar%2) == 1) $prefix = $prefixtemp;

		$string = $prefix . $ellipsis . $suffix;
	}

	return $string;

}
?>
