<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    function get_pf_version()
    {
        return '1.1';
    }

    function getPreviewString()
    {
        $preview = 'document.addEventListener("DOMContentLoaded", function (f){
				if("undefined"!=typeof CKEDITOR&&null!=CKEDITOR)
				{
				CKEDITOR.on("instanceLoaded", function(e){
						var m=document.createElement("div");
						m.setAttribute("id","qa-cke-prev");
						document.querySelector(".cke").parentNode.appendChild(m);
						e.editor.on("change",function(){
                                var x=document.getElementById("qa-cke-prev");
                                x.innerHTML=e.editor.getData();';
        if (qa_opt('qa-mathjax-enable')) {
            $preview .= 'MathJax.Hub.Queue([\'Typeset\', MathJax.Hub, "qa-cke-prev"]);';
        }
        if (qa_opt('qa-prettify-enable')) {
            $preview .= 'x.querySelectorAll(\'pre code\').forEach((block) => { hljs.highlightBlock(block); });';
        }
        $preview .= '});m.innerHTML=e.editor.getData(true);';
        if (qa_opt('qa-mathjax-enable')) {
            $preview .= 'MathJax.Hub.Queue([\'Typeset\', MathJax.Hub, "qa-cke-prev"]);';
        }
        if (qa_opt('qa-prettify-enable')) {
            $preview .= 'm.querySelectorAll(\'pre code\').forEach((block) => { hljs.highlightBlock(block); });';
        }
        $preview .= '});}});';
        return $preview;
    }

    function body_suffix()
    {
        qa_html_theme_base::body_suffix();
        {
            if (qa_opt("qa-mathjax-enable") && qa_opt('qa-mathjax-config') && qa_opt('qa-mathjax-url')) {
                $this->output('<script  type="text/x-mathjax-config">' . qa_opt('qa-mathjax-config') . '</script>');
                $this->output('<script  async type="text/javascript" src="' . qa_opt('qa-mathjax-url') . '"></script>');
            }
            if (qa_opt("qa-ckepreview-enable")) {
                $this->output('<script type="text/javascript">' . $this->getPreviewString() . '</script>');
            }
        }
    }

    function head_custom()
    {
        qa_html_theme_base::head_custom();
        if (qa_opt("qa-prettify-enable")) {
            $this->output('<link rel="stylesheet" href="' . qa_opt('qa-formatter-css') . '">');
            $this->output('<script type="text/javascript" src="' . qa_opt('qa-prettify-url') . '"></script>');
            $this->output('<script>hljs.initHighlightingOnLoad();</script>');
        }
        if (qa_opt("qa-ckepreview-enable")) {
            $this->output('<style type="text/css">
#qa-cke-prev{border-top:2px dashed #def; border-bottom:2px dashed #def; padding-top:10px; margin-top:10px; }
</style>');
        }
    }
}

?>
