<?php

class qa_html_theme_layer extends qa_html_theme_base {
	function get_pf_version()
	{
		return '1.1.1';
	}

	function getKatexConfig()
	{
		return '
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" crossorigin="anonymous">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" crossorigin="anonymous"
    onload="katexReady()"></script>
<script>
function _texFromHtml(html) {
    var s = html;
    /* Convert block-level tags to newlines */
    s = s.replace(/<br\\s*\\/?>/gi, "\\n");
    s = s.replace(/<\\/p>/gi, "\\n");
    s = s.replace(/<p[^>]*>/gi, "");
    s = s.replace(/<\\/div>/gi, "\\n");
    s = s.replace(/<div[^>]*>/gi, "");
    /* Handle common HTML entities */
    s = s.replace(/&nbsp;/gi, " ");
    s = s.replace(/&lt;/gi, "<");
    s = s.replace(/&gt;/gi, ">");
    s = s.replace(/&amp;/gi, "&");
    /* Remove other HTML tags but keep content */
    s = s.replace(/<[^>]+>/g, "");
    /* Decode remaining HTML entities */
    var tmp = document.createElement("textarea");
    tmp.innerHTML = s;
    return tmp.value;
}
function _preprocessDisplayMath(el) {
    if (typeof katex === "undefined") return;
    var html = el.innerHTML, orig = html;
    html = html.replace(/\\$\\$([\\s\\S]*?)\\$\\$/g, function(m, tex) {
        try { return katex.renderToString(_texFromHtml(tex), {displayMode: true, throwOnError: false}); }
        catch(e) { return m; }
    });
    html = html.replace(/\\$([^\\$]*?\\\\begin\\{[^\\$]*?)\\$/g, function(m, tex) {
        try { return katex.renderToString(_texFromHtml(tex), {displayMode: false, throwOnError: false}); }
        catch(e) { return m; }
    });
    html = html.replace(/\\\\\\[([\\s\\S]*?)\\\\\\]/g, function(m, tex) {
        try { return katex.renderToString(_texFromHtml(tex), {displayMode: true, throwOnError: false}); }
        catch(e) { return m; }
    });
    html = html.replace(/\\\\begin\\{([^}]+)\\}([\\s\\S]*?)\\\\end\\{\\1\\}/g, function(m, env, inner) {
        try {
            var tex = "\\\\begin{" + env + "}" + _texFromHtml(inner) + "\\\\end{" + env + "}";
            return katex.renderToString(tex, {displayMode: true, throwOnError: false});
        } catch(e) { return m; }
    });
    if (html !== orig) el.innerHTML = html;
}
var _katexOpts = {
    delimiters: [
        {left: "$$", right: "$$", display: true},
        {left: "$",  right: "$",  display: false},
        {left: "\\\\(", right: "\\\\)", display: false},
        {left: "\\\\[", right: "\\\\]", display: true}
    ],
    throwOnError: false
};
var _katexQueue = [];
function katexReady() {
    /* Preprocess only specific content areas that may have multiline LaTeX in HTML */
    var contentAreas = document.querySelectorAll(".qa-q-view-content, .qa-q-view-content1, .qa-a-item-content, .qa-c-item-content, .entry-content, .post-content, .qa-form-tall-text");
    contentAreas.forEach(function(el) { _preprocessDisplayMath(el); });
    /* Then let auto-render handle the rest */
    renderMathInElement(document.body, _katexOpts);
    while (_katexQueue.length) { var el = _katexQueue.shift(); if(el) { _preprocessDisplayMath(el); renderMathInElement(el, _katexOpts); } }
}
function typeset(code) {
    try {
        var els = code();
        if (!Array.isArray(els)) els = [els];
        els.forEach(function(el) {
            if (!el) return;
            if (typeof renderMathInElement === "function") {
                _preprocessDisplayMath(el);
                renderMathInElement(el, _katexOpts);
            } else {
                _katexQueue.push(el);
            }
        });
    } catch(e) { console.log("typeset failed: " + e.message); }
    return Promise.resolve();
}
</script>
		';
	}

	function getPreviewString()
	{

		$preview	 ='document.addEventListener("DOMContentLoaded", function (f){


			if("undefined"!=typeof CKEDITOR&&null!=CKEDITOR)
			{
				CKEDITOR.on("instanceLoaded",
					function(e){
						var m=document.createElement("div");
						m.setAttribute("id","qa-cke-prev");
						document.querySelector(".cke").
							parentNode.appendChild(m);
						e.editor.on("change",function(){document.getElementById("qa-cke-prev").innerHTML=e.editor.getData();';
						if(qa_opt('qa-mathjax-enable'))
						{
						$preview .= '
typeset(() => {
  const math = document.querySelector("#qa-cke-prev");
  return [math];
});

';
						}
						if(qa_opt('qa-prettify-enable'))
						{
							$preview .= 'prettyPrint();';
						}

						$preview .= '
								}
							   );
						m.innerHTML=e.editor.getData(true);';
						if(qa_opt('qa-mathjax-enable'))
						{
						$preview .= '
							typeset(() => {
  const math = document.querySelector("#qa-cke-prev");
  return [math];
});
';

						}
						if(qa_opt('qa-prettify-enable'))
						{
							$preview .= 'prettyPrint();';
						}
						$preview .= '});}});';
						return $preview;

	}

	function body_suffix()
	{

		qa_html_theme_base::body_suffix();
		$allowed_templates = array("question", "questions", "blog", "blogs", "qp-quickeditcat-page", "revisions", "ask", "activity", "tag", "user-activity", "user-questions", "user-answers", "unanswered", "search", "qa", "admin", "home", 'user-list', 'qp-quickedit-page', 'category');
		if(in_array($this->template, $allowed_templates))
		{
			if(qa_opt("qa-mathjax-enable"))
			{
				if(qa_opt('qa-katex-enable'))
				{
					$this->output($this->getKatexConfig());
				}
				else if(qa_opt('qa-mathjax-config'))
				{
					$this->output(qa_opt('qa-mathjax-config'));
				}
			}
			if(qa_opt("qa-prettify-enable") && (!qa_opt("qa-ckepreview-enable")))// || ($this->template !== 'ask')))
			{
				$this->output('<script async type="text/javascript" src="'.qa_opt('qa-prettify-url').'"></script>');
			}
			else if(qa_opt("qa-ckepreview-enable"))
			{
				$version = $this->get_pf_version();
				if(qa_opt("qa-prettify-enable"))
				{
					$this->output('
							<script  type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'pf/prettify.js?v='.$version.'"></script>');
				}
				$this->output('<script type="text/javascript">'.$this->getPreviewString().'prettyPrint();</script>');
			}
		}


	}
	function head_custom()
	{

		qa_html_theme_base::head_custom();
		$allowed_templates = array("question", "questions", "blog", "blogs", "qp-quickeditcat-page", "revisions", "ask", "activity", "tag", "user-activity", "user-questions", "user-answers", "unanswered", "search", "qa", "admin", "home", 'user-list', 'qp-quickedit-page');
		if(in_array($this->template, $allowed_templates))
		{
		if(qa_opt("qa-ckepreview-enable") && qa_opt("qa-prettify-enable"))
		{
			$version = $this->get_pf_version();
			//$this->output('<link rel="stylesheet" defer async type="text/css" href="'.QA_HTML_THEME_LAYER_URLTOROOT.'pf/prettify.css?v='.$version.'">');
		}
			if(qa_opt("qa-ckepreview-enable"))
			{
				$this->output('<style type="text/css">'.
'#qa-cke-prev{
border-top:2px dashed #def; border-bottom:2px dashed #def; padding-top:10px; margin-top:10px;
}
'

.'</style>');
			}

		$this->output('<style type="text/css">'.qa_opt('qa-formatter-css').'</style>');
		}
	}

	function head_metas()
	{
		if (
			$this->template === 'question' &&
			qa_opt('qa-codecogs-meta-description-enable') &&
			strlen($this->content['description'] ?? '') &&
			function_exists('qa_mathjax_convert_to_codecogs')
		) {
			$decoded = html_entity_decode($this->content['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
			$converted = qa_mathjax_convert_to_codecogs($decoded, 'url');
			$this->content['description'] = qa_html($converted);
		}

		qa_html_theme_base::head_metas();
	}
}
?>
