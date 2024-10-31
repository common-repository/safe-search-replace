<?php

class Ssr {
	public static function init() {
		self::$api = dirname(__FILE__) . '/';
		
		add_action('admin_init', 'Ssr::onAdmin');
		add_action('admin_menu', 'Ssr::initAdminMenu');
		add_action('wp_ajax_safesearchreplace', 'Ssr::ajaxHandler');
		
		define ('SSR_PATH_DATA', dirname(__FILE__).'/../safe-search-replace-data');
	}
	
	/* PLUGIN INIT */
	public static function onAdmin() {
		
	}
	
	public static function initAdminMenu() {
		Ssr::loadClass('Ssr_UI_Workspace');
		Ssr_UI_Workspace::init();
		
		Ssr::loadClass('Ssr_UI_SimpleSearch');
		Ssr_UI_SimpleSearch::init();
		
		Ssr::loadClass('Ssr_UI_Shortcode');
		Ssr_UI_Shortcode::init();
		
		Ssr::loadClass('Ssr_UI_Undo');
		Ssr_UI_Undo::init();
	}
	
	
	public static function loadClass($classname) {
		require_once self::$api . str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';
	}
	
	
	/* AJAX */
	public static function ajaxHandler() {
		if (isset($_POST['class']) && !empty($_POST['class'])) {
			$args = $_POST;
			unset($args['class']);
			$method = 'ajax';
			if (isset($_POST['method']) && !empty($_POST['method'])) {
				$method = $_POST['method'];
				unset($_POST['method']);
			}
			Ssr::loadClass($_POST['class']);
			$o = new $_POST['class']();
			$o->$method($args);
		}
		exit;
	}
		
	
	/* MISC */	
	public static function version() {
		return '2.0.1';
	}
	
	public static function author() {
		return 'Benjamin Sommer';
	}
	
	public static function website() {
		return 'http://ssr.benjaminsommer.com';
	}
	
	private static $api;	
}