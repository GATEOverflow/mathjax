<?php

class qa_formatter_admin
{

    function allow_template($template)
    {
        return ($template != 'admin');
    }

    function option_default($option)
    {

        switch ($option) {
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
});';
            case 'qa-mathjax-url':
                return 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-AMS-MML_HTMLorMML';
            case 'qa-prettify-url':
                return 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js';
            case 'qa-formatter-css':
                return 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/default.min.css';
            case 'qa-mathjax-enable':
                return '1';
            case 'qa-prettify-enable':
                return '1';
            case 'qa-ckepreview-enable':
                return '1';
            default:
                return null;

        }
    }

    function admin_form(&$qa_content)
    {

        //	Process form input

        $ok = null;
        if (qa_clicked('formatter-save-button')) {
            foreach ($_POST as $i => $v) {

                qa_opt($i, $v);
            }
            if (!isset($_POST['qa-mathjax-enable']))
                qa_opt('qa-mathjax-enable', '0');
            if (!isset($_POST['qa-prettify-enable']))
                qa_opt('qa-prettify-enable', '0');
            if (!isset($_POST['qa-ckepreview-enable']))
                qa_opt('qa-ckepreview-enable', '0');

            $ok = qa_lang('admin/options_saved');
        } else if (qa_clicked('mathjax-reset-button')) {
            foreach ($_POST as $i => $v) {
                $def = $this->option_default($i);
                if ($def !== null) qa_opt($i, $def);
            }
            $ok = qa_lang('admin/options_reset');
        }
        //	Create the form for display


        $fields = array();


        $fields[] = array(
            'label' => '<a href="http://docs.mathjax.org/en/latest/configuration.html">MathJax Configuration</a>',
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
            'label' => 'highlight.js URL',
            'tags' => 'NAME="qa-prettify-url"',
            'value' => qa_opt('qa-prettify-url'),
            'type' => 'text',
        );
        $fields[] = array(
            'label' => 'highlight.js Style URL',
            'tags' => 'NAME="qa-formatter-css"',
            'value' => qa_opt('qa-formatter-css'),
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
