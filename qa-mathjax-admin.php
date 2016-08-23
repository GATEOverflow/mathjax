<?php
class qa_mathjax_admin {

	function allow_template($template)
	{
		return ($template!='admin');
	}

	function option_default($option) {

		switch($option) {
		case 'qa-mathjax-config':
				return '
  MathJax.Hub.Config({

    tex2jax: {
      inlineMath: [ [\'$\',\'$\'], ["\\\\(","\\\\)"] ],
     config: ["MMLorHTML.js"],
      jax: ["input/TeX"],
      processEscapes: true
    }
  });

MathJax.Hub.Config({
"HTML-CSS": {
linebreaks: {
automatic: true
}
}
  });

				';
			case 'qa-mathjax-url':
				return 'https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML';
			case 'qa-pretiffy-url':
				return 'https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js';
			case 'qa-mathjax-enable':
				return '1';
			case 'qa-pretiffy-enable':
				return '1';
			default:
				return null;

		}
	}
	function admin_form(&$qa_content)
	{

		//	Process form input

		$ok = null;
		if (qa_clicked('mathjax-save-button')) {
			foreach($_POST as $i => $v) {

				qa_opt($i,$v);
			}
			if(!isset($_POST['qa-mathjax-enable']))
                                qa_opt('qa-mathjax-enable', '0');
			if(!isset($_POST['qa-pretiffy-enable']))
                                qa_opt('qa-pretiffy-enable', '0');

			$ok = qa_lang('admin/options_saved');
		}
		else if (qa_clicked('mathjax-reset-button')) {
			foreach($_POST as $i => $v) {
				$def = $this->option_default($i);
				if($def !== null) qa_opt($i,$def);
			}
			$ok = qa_lang('admin/options_reset');
		}			
		//	Create the form for display


		$fields = array();


		$fields[] = array(
				'label' => '<a href="http://docs.mathjax.org/en/latest/configuration.html>MathJax Configuration</a>',
				'tags' => 'NAME="qa-mathjax-config"',
				'value' => qa_opt('qa-mathjax-config'),
				'type' => 'textarea',
				'rows' => 20
				);
		$fields[] = array(
				'label' => 'MathJax URL',
				'tags' => 'NAME="qa-mathjax-url"',
				'value' => qa_opt('qa-mathjax-url'),
				'type' => 'text',
				);
		$fields[] = array(
				'label' => 'Pretiffy URL',
				'tags' => 'NAME="qa-pretiffy-url"',
				'value' => qa_opt('qa-pretiffy-url'),
				'type' => 'text',
				);
		$fields[] = array(
				'label' => 'Enable MathJax',
				'tags' => 'NAME="qa-mathjax-enable"',
				'value' => qa_opt('qa-mathjax-enable'),
				'type' => 'checkbox',
				);
		$fields[] = array(
				'label' => 'Enable Pretiffy',
				'tags' => 'NAME="qa-pretiffy-enable"',
				'value' => qa_opt('qa-pretiffy-enable'),
				'type' => 'checkbox',
				);


		return array(
				'ok' => ($ok && !isset($error)) ? $ok : null,

				'fields' => $fields,

				'buttons' => array(
					array(
						'label' => qa_lang_html('main/save_button'),
						'tags' => 'NAME="mathjax-save-button"',
					     ),
					array(
						'label' => qa_lang_html('admin/reset_options_button'),
						'tags' => 'NAME="mathjax-reset-button"',
					     ),
					),
			    );
	}
	function getMyPath($location) { 
		$getMyPath = str_replace($_SERVER['DOCUMENT_ROOT'],'',$location); 
		return $getMyPath; 
	} 


}
