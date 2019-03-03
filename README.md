# Q2A Formatter
Question2answer Formatter

This plugin provides an option to add MathJax script, Prettify script and a Preview for CKEditor and enables them by default on all pages.

For editor support please use this [modified ckeditor](https://github.com/tangruize/q2a-formatter/releases).

Download the ckeditor and place inside qa-plugin/wysiwyg-editor folder. 

I also recommend you to install [q2apro-warn-on-leave](https://github.com/q2apro/q2apro-warn-on-leave) plugin, which warns the user that text area has been changed when he is leaving.

This is a beta code, use it at your own risk on a production environment. 

# Default Configuration
MathJax Configuration:
```
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
```

MathJax URL:
```
https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-AMS-MML_HTMLorMML
```

highlight.js URL:
```
https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js
```

highlight.js Style URL:
```
https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/default.min.css
```

More highlight.js and styles: https://cdnjs.com/libraries/highlight.js/


highlight.js demo: https://highlightjs.org/static/demo/
