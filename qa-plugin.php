<?php



	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
			header('Location: ../../');
			exit;
	}
	
	qa_register_plugin_layer('qa-mathjax-layer.php', 'Mathjax Layer');	
	
	qa_register_plugin_module('module', 'qa-mathjax-admin.php', 'qa_mathjax_admin', 'MathJax Admin');

/*
	Omit PHP closing tag to help avoid accidental output
*/
