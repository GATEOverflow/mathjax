<?php

class qa_mathjax_widget {

	var $urltoroot;

	function load_module($directory, $urltoroot)
	{
		$this->urltoroot = $urltoroot;
	}

	function allow_template($template)
	{
		if($template == 'question')
			return false;
		return true;
	}

	function allow_region($region)
	{
		return true;

	}

	function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
	{
		if(qa_opt("qa-mathjax-enable") && qa_opt('qa-mathjax-config') && qa_opt('qa-mathjax-url'))
		{
			$themeobject->output('<script  type="text/x-mathjax-config">'. qa_opt('qa-mathjax-config').'</script>');
			$themeobject->output('<script  async type="text/javascript" src="'.qa_opt('qa-mathjax-url').'"></script>');
		}
		if(qa_opt("qa-pretiffy-enable"))
		{
			$themeobject->output('<script  async type="text/javascript" src="'.qa_opt('qa-pretiffy-url').'"></script>');
		}

	}
};


/*
   Omit PHP closing tag to help avoid accidental output
 */
