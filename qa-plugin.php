<?php



	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
			header('Location: ../../');
			exit;
	}
	
	qa_register_plugin_layer('qa-formatter-layer.php', 'Formatter Layer');	
	
	qa_register_plugin_module('module', 'qa-formatter-admin.php', 'qa_formatter_admin', 'Formatter Admin');

/*
	Omit PHP closing tag to help avoid accidental output
*/
