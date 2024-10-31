<?php

/*
 	Plugin Name: Safe Search Replace
	Plugin URI: http://ssr.benjaminsommer.com
	Description: Safely search and replace shortcodes and post content. You can <strong>undo changes</strong> if required.
	Author: Benjamin Sommer
	Version: 2.0.1
	Author URI: http://benjaminsommer.com
	License: CC GNU GPL 2.0 license
	Text Domain: ssr

	Coding standard: http://framework.zend.com/manual/en/coding-standard.html
*/

// Initialize Plugin and declare API
require 'Ssr.php';
Ssr::init();
Ssr::loadClass('Ssr_Plugin');
register_activation_hook(__FILE__, 'Ssr_Plugin::activate');
register_deactivation_hook(__FILE__, 'Ssr_Plugin::deactivate');
register_uninstall_hook(__FILE__, 'Ssr_Plugin::uninstall');