<?php



if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_layer('qa-formatter-layer.php', 'Formatter Layer');	
qa_register_plugin_overrides('qa-formatter-overrides.php', 'Formatter Overrides');	

qa_register_plugin_module('module', 'qa-formatter-admin.php', 'qa_formatter_admin', 'Formatter Admin');

/*
	Omit PHP closing tag to help avoid accidental output
 */

function mathpix_get_text($imgurl) {
	$ch = curl_init();
	$action = "https://api.mathpix.com/v3/text";
	$header_file = file_get_contents(__DIR__."/mathpix.json");
	if(!$header_file) {
		return NULL;
	}
	$header = json_decode($header_file, true);
	$params = array();
	$params['src'] = $imgurl;
	$params['math_inline_delimiters'] = array("$", "$");
	$params['rm_spaces'] = 'true';
	//print_r($params);
	curl_setopt($ch, CURLOPT_URL, $action);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	curl_setopt($ch ,CURLOPT_RETURNTRANSFER, true);

	$curlresult = curl_exec($ch);
	curl_close($ch);
	error_log($curlresult);
	$response = json_decode($curlresult, true);
	if($response['confidence'] >= 0)
	{
		if(isset($response['text']))
			$text = $response['text'];
		else
			$text = $response['latex_styled'];
		//			error_log($text);
	}
	else {
		$text = Null;
	}
	return $text;
}
function mathpix_process_post_content($content, $process_options=false) {
	$newcontent = $content;
	preg_match_all('/<img[^>]+>/i',$content, $imgTags, PREG_SET_ORDER);
	//error_log(json_encode($imgTags));
	for($l=0; $l < count($imgTags); $l++) {
		preg_match('/src="([^"]+)/i',$imgTags[$l][0], $image);
		//error_log(json_encode($image));
		if((bool)qa_post_text('rollback')) {
		}
		else {
			$imgsrc = html_entity_decode(str_ireplace( 'src="', '',  $image[0]));
			$text = mathpix_get_text($imgsrc);
			if($text) {
				$newcontent .= "<p>".nl2br($text)."</p>";
			}
			else {//not used
				continue;
/*
						//$tmpfolder = dirname(__FILE__)."/tmp/".uniqid();
						$wget_cmd = '/usr/bin/curl "'.$imgsrc.'" --create-dirs -o '.$tmpfolder.'/a.png';
						error_log($wget_cmd);
						$ret = system($wget_cmd);
						if($ret != 0 ) {
							echo "Error code $ret for postid $postid";
						}
						else {
							$updated = false;
							system("/usr/bin/convert -resize 500% -type Grayscale -threshold 60% $tmpfolder/a.png $tmpfolder/b.png");
							system("/usr/bin/tesseract $tmpfolder/b.png $tmpfolder/b");
							$text = file_get_contents("$tmpfolder/b.txt");
							$text = preg_replace('/[[:^print:]]/', '', $text);
							$pattern ='/(.*)(\(A\))(.*)(\((B|8)\))(.*)(\(C\))(.*)(\(D\))(.*)/i';
							$replacement = '$1<ol style="list-style-type:upper-alpha"><li> $3 </li><li> $6 </li> <li>$8 </li>  <li>$10  </li></ol>';
							$text = preg_replace($pattern, $replacement, $text);
							$imagetext[] = $text;
						}
						//                              print_r($text);
}*/
			}

		}
	}
	if($process_options) {
		$newcontent = mathpix_process_options($newcontent);
	}
	return $newcontent;
}
function mathpix_process_post($postid, $content, $process_options = false) {
	$newcontent = mathpix_process_post_content($content, $process_options);
	if($content !== $newcontent) {
		qa_post_set_content($postid, null, $newcontent, null, null, null,null, qa_get_logged_in_userid(), null, null);
	}
}

function mathpix_process_ocr($tag, $process_options=false) {
	$query = "select b.postid,b.content from ^posttags a,^posts b  where a.postid = b.postid and wordid = (select wordid from ^words WHERE  word = '".qa_strtolower($tag)."')";
	//	error_log($query);

	$result = qa_db_query_sub($query);
	$posts = qa_db_read_all_assoc($result);
	$count = 0;
	foreach ($posts as $post) {
		$postid = $post['postid'];
		$content = $post['content'];
		mathpix_process_post($postid, $content, $process_options);
		$count++;
	}
	return $count;
}
function mathpix_process_options($content) {

	$text = $content;
	$pattern1a ='/(.*\s*)(\(a\))(.+)\s+(\(b\))(.+)\s+(\(c\))(.+)\s+(\(d\))(.+)\s+(\(e\))(.+?)$/';
	$pattern1b ='/(.*\s*)(\(a\))(.+)\s+(\(b\))(.+)\s+(\(c\))(.+)\s+(\(d\))(.+?)$/';
	$pattern1c ='/(.*\s*)(\(a\.\))(.+)\s+(\(b\.\))(.+)\s+(\(c\.\))(.+)\s+(\(d\.\))(.+)\s+(\(e\.\)) (.+?)$/';
	$pattern1d ='/(.*\s*)(\(a\.\))(.+)\s+(\(b\.\))(.+)\s+(\(c\.\))(.+)\s+(\(d\.\))(.+?)$/';
	$pattern1e ='/(.*\s*)(a\.)(.+)\s+(b\.)(.+)\s+(c\.)(.+)\s+(d\.)(.+)\s+(e\.)(.+?)$/';
	$pattern1f ='/(.*\s*)(a\.)(.+)\s+(b\.)(.+)\s+(c\.)(.+)\s+(d\.)(.+?)$/';
	
	$pattern1 ='/(.*\s*)(\(\s*A\s*\))(.+)\s+(\(\s*B\s*\))(.+)\s+(\(\s*C\s*\))(.+)\s+(\(\s*D\s*\))(.+?)$/s';
	$pattern3 ='/(.*\s*)(\s*A\.\s*)(.+)\s+(\s*B\.\s*)(.+)\s+(\s*C\.\s*)(.+)\s+(\s*D\.\s*)(.+?)$/s';
	$pattern4 ='/(.*\s*)(\s*A\)\s*)(.+)\s+(\s*B\)\s*)(.+)\s+(\s*C\)\s*)(.+)\s+(\s*D\)\s*)(.+?)$/s';
	
	$pattern11 ='/(.*\s*)(\(\s*I\s*\))(.+)\s+(\(\s*II\s*\))(.+)\s+(\(\s*III\s*\))(.+)\s+(\(\s*IV\s*\))(.+?)<br \/>/s';
	$pattern12 ='/(.*\s*)(\(i\))(.+)\s+(\(ii\))(.+)\s+(\(iii\))(.+)\s+(\(iv\))(.+?)<br \/>/';
	$pattern13 ='/(.*\s*)(\s*I\.\s*)(.+)\s+(\s*II\.\s*)(.+)\s+(\s*III\.\s*)(.+)\s+(\s*IV\.\s*)(.+?)<br \/>/s';
	$pattern14 ='/(.*\s*)(\s*i\.\s*)(.+)\s+(\s*ii\.\s*)(.+)\s+(\s*iii\.\s*)(.+)\s+(\s*iv\.\s*)(.+?>)<br \/>/s';
	
	$replacement1a = '$1<ol style="list-style-type:upper-alpha"><li> $3 </li><li> $5 </li> <li>$7 </li>  <li>$9  </li> <li> $11</li></ol>';
	$replacement1b = '$1<ol style="list-style-type:upper-alpha"><li> $3 </li><li> $5 </li> <li>$7 </li>  <li>$9  </li></ol>';
	$replacement2a = '$1<ol style="list-style-type:upper-alpha"><li> $3 </li><li> $5 </li> <li>$7 </li><li>$9 </li></ol>';

	$replacementone = '$1<ol style="list-style-type:upper-roman"><li> $3 </li><li> $5 </li> <li>$7 </li>  <li>$9  </li></ol>';
	$replacementone2 = '$1<ol style="list-style-type:lower-roman"><li> $3 </li><li> $5 </li> <li>$7 </li>  <li>$9  </li></ol>';
	$text = preg_replace($pattern1a, $replacement1a, $text, -1, $c1);
	$text = preg_replace($pattern1b, $replacement1b, $text, -1, $c1);
	$text = preg_replace($pattern1c, $replacement1b, $text, -1, $c1);
	$text = preg_replace($pattern1d, $replacement1b, $text, -1, $c1);
	$text = preg_replace($pattern1e, $replacement1b, $text, -1, $c1);
	$text = preg_replace($pattern1f, $replacement1b, $text, -1, $c1);
	
	$text = preg_replace($pattern1, $replacement1b, $text, -1, $count);
	$text = preg_replace($pattern3, $replacement1b, $text, -1, $count);
	$text = preg_replace($pattern4, $replacement1b, $text, -1, $count);

	$text = preg_replace($pattern13, $replacementone, $text, -1, $count);
	$text = preg_replace($pattern14, $replacementone2, $text, -1, $count);
	return $text;


}

