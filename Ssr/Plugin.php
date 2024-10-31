<?php

class Ssr_Plugin {
	
	public function install() {
		
	}
	
	public function activate() {
		if (!is_dir(SSR_PATH_DATA))
			mkdir(SSR_PATH_DATA, 0777);
	}
	
	public function deactivate() {
		
	}
	
	public function reset() {
		
	}
	
	public function uninstall() {
		if (!is_dir(SSR_PATH_DATA))
			rmdir(SSR_PATH_DATA);
	}
}