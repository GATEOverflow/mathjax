<?php

class qa_html_theme_layer extends qa_html_theme_base {

	function body_suffix()
	{
		qa_html_theme_base::body_suffix();
		if($this->template == 'question')
		{
			if(qa_opt("qa-mathjax-enable") && qa_opt('qa-mathjax-config') && qa_opt('qa-mathjax-url'))
			{
				$this->output('<script  type="text/x-mathjax-config">'. qa_opt('qa-mathjax-config').'</script>');
				$this->output('<script  async type="text/javascript"> src="'.qa_opt('qa-mathjax-url').'"</script>');
			}
			if(qa_opt("qa-pretiffy-enable"))
			{
				$this->output('<script  async type="text/javascript"> src="'.qa_opt('qa-pretiffy-url').'"</script>');
			}
		}


	}
}
?>
