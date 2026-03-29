<?php
class qa_formatter_admin {
	private function codecogsEndpointOptions() {
		return array(
			'png.image' => 'PNG (png.image)',
			'svg.image' => 'SVG (svg.image)',
		);
	}

	private function normalizeCodecogsEndpoint($value) {
		$value = qa_strtolower(trim((string)$value));
		$options = $this->codecogsEndpointOptions();

		if (!isset($options[$value])) {
			return 'png.image';
		}

		return $value;
	}

	function allow_template($template)
	{
		return ($template!='admin');
	}

	function option_default($option) {

		switch($option) {
		case 'qa-formatter-css':
				return file_get_contents(dirname(__FILE__).'/custom.css');
		case 'qa-mathjax-config':
				return '
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" crossorigin="anonymous">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" crossorigin="anonymous"
    onload="katexReady()"></script>
<script>
var _katexOpts = {
    delimiters: [
        {left: "$$", right: "$$", display: true},
        {left: "$",  right: "$",  display: false},
        {left: "\\(", right: "\\)", display: false},
        {left: "\\[", right: "\\]", display: true}
    ],
    throwOnError: false
};
function katexReady() { renderMathInElement(document.body, _katexOpts); }
function typeset(code) {
    try {
        var els = code();
        if (!Array.isArray(els)) els = [els];
        els.forEach(function(el) {
            if (el && typeof renderMathInElement === "function") renderMathInElement(el, _katexOpts);
        });
    } catch(e) {}
    return Promise.resolve();
}
</script>
				';
			case 'qa-mathjax-url':
				return 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML';
			case 'qa-prettify-url':
				return 'https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js';
			case 'qa-mathjax-enable':
				return '1';
			case 'qa-prettify-enable':
				return '1';
			case 'qa-ckepreview-enable':
				return '1';
			case 'qa-codecogs-feed-enable':
				return '1';
			case 'qa-codecogs-meta-description-enable':
				return '0';
			case 'qa-codecogs-render-endpoint':
				return 'png.image';
			default:
				return null;

		}
	}
	function admin_form(&$qa_content)
	{

		//	Process form input

		$ok = null;
		if (qa_clicked('formatter-save-button')) {
			foreach($_POST as $i => $v) {

				qa_opt($i,$v);
			}
			if(!isset($_POST['qa-mathjax-enable']))
                                qa_opt('qa-mathjax-enable', '0');
			if(!isset($_POST['qa-prettify-enable']))
                                qa_opt('qa-prettify-enable', '0');
			if(!isset($_POST['qa-ckepreview-enable']))
                                qa_opt('qa-ckepreview-enable', '0');
			if(!isset($_POST['qa-codecogs-feed-enable']))
				qa_opt('qa-codecogs-feed-enable', '0');
			if(!isset($_POST['qa-codecogs-meta-description-enable']))
				qa_opt('qa-codecogs-meta-description-enable', '0');

			qa_opt('qa-codecogs-render-endpoint', $this->normalizeCodecogsEndpoint(qa_post_text('qa-codecogs-render-endpoint')));

			$ok = qa_lang('admin/options_saved');
		}
		else if (qa_clicked('formatter-reset-button')) {
			foreach($_POST as $i => $v) {
				$def = $this->option_default($i);
				if($def !== null) qa_opt($i,$def);
			}
			$ok = qa_lang('admin/options_reset');
		}			
		//	Create the form for display


		$fields = array();


		$fields[] = array(
				'label' => 'Custom CSS for Content Formatting',
				'tags' => 'NAME="qa-formatter-css"',
				'value' => qa_opt('qa-formatter-css'),
				'type' => 'textarea',
				'rows' => 20
				);
		$fields[] = array(
				'label' => '<a href="http://docs.mathjax.org/en/latest/configuration.html">MathJax Configuration</a>',
				'tags' => 'NAME="qa-mathjax-config"',
				'value' => qa_opt('qa-mathjax-config'),
				'type' => 'textarea',
				'rows' => 20
				);
		$fields[] = array(
				'label' => 'Prettify URL',
				'tags' => 'NAME="qa-prettify-url"',
				'value' => qa_opt('qa-prettify-url'),
				'type' => 'text',
				);
		$fields[] = array(
				'label' => 'Enable MathJax',
				'tags' => 'NAME="qa-mathjax-enable"',
				'value' => qa_opt('qa-mathjax-enable'),
				'type' => 'checkbox',
				);
		$fields[] = array(
				'label' => 'Enable Prettify',
				'tags' => 'NAME="qa-prettify-enable"',
				'value' => qa_opt('qa-prettify-enable'),
				'type' => 'checkbox',
				);
		$fields[] = array(
				'label' => 'Enable CKEditor Preview',
				'tags' => 'NAME="qa-ckepreview-enable"',
				'value' => qa_opt('qa-ckepreview-enable'),
				'type' => 'checkbox',
				);
		$fields[] = array(
				'label' => 'Convert TeX to CodeCogs images in feeds',
				'tags' => 'NAME="qa-codecogs-feed-enable"',
				'value' => qa_opt('qa-codecogs-feed-enable'),
				'type' => 'checkbox',
				);
		$fields[] = array(
				'label' => 'CodeCogs render endpoint (png.image or svg.image)',
				'tags' => 'NAME="qa-codecogs-render-endpoint"',
				'value' => $this->normalizeCodecogsEndpoint(qa_opt('qa-codecogs-render-endpoint')),
				'options' => $this->codecogsEndpointOptions(),
				'type' => 'select',
				);
		$fields[] = array(
				'label' => 'Convert TeX to CodeCogs URLs in question meta description',
				'tags' => 'NAME="qa-codecogs-meta-description-enable"',
				'value' => qa_opt('qa-codecogs-meta-description-enable'),
				'type' => 'checkbox',
				);


		return array(
				'ok' => ($ok && !isset($error)) ? $ok : null,

				'fields' => $fields,

				'buttons' => array(
					array(
						'label' => qa_lang_html('main/save_button'),
						'tags' => 'NAME="formatter-save-button"',
					     ),
					array(
						'label' => qa_lang_html('admin/reset_options_button'),
						'tags' => 'NAME="formatter-reset-button"',
					     ),
					),
			    );
	}

}
