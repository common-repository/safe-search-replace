<?php

class Ssr_UI_Workspace {
	public static function init() {
		add_menu_page( 'Safe Search Replace - Workspace', 'Search & Replace', 'publish_posts', 'ssr-workspace', array(new Ssr_UI_Workspace(), 'display') );
	}
	
	public function display() {
		echo '<div class="wrap about-wrap">';
		echo '<h1>Safe Search Replace</h1>';
		echo '<div class="about-text">
        			Thank you for updating to the latest version! <br />Safe Search Replace '. Ssr::version() .' is already making your administration tasks easier and 
        			faster.</div>';
		echo '<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" name="whatsnew" style="cursor:pointer"> What’s New </a>
				<a class="nav-tab" name="credits" style="cursor:pointer"> Credits </a>
			</h2> 
		<div class="changelog point-releases" style="display:nones" id="whatsnew">
			<h2>Shortcodes</h2>
			<p>Quite a lot of plugins use shortcodes - deactivating them leaves your shortcodes unused and plain to your visitors. 
				With this feature, you can remove existing shortcodes, rename them (in case of an update or plugin switch), and even remove, rename and add their attributes.</p>
			<h2>Search and replace made easy</h2>
			<p>Easily search in your post titles, contents, excerpts and comments for words and replace them with something else. 
				A visual preview is almost instant, using modern AJAX technologies.</p>
			<h2>Undo recent tasks</h2>
			<p>All operations, or tasks which you can do with this plugin can be undone safely anytime later. Current, the undo history is cleared after Plugin update.</p>
		</div>
		<div class="changelog point-releases" style="display:none" id="credits">
			<p><strong>Plugin Author</strong><br />Benjamin Sommer</p>
			<p><strong>Plugin Version</strong><br />'. Ssr::version() .'</p>
			<p><strong>Plugin Homepage</strong><br /><a href="'. Ssr::website() .'">'. Ssr::website() .'</a></p>
		</div>';
		
		echo "<script>
			jQuery('.nav-tab-wrapper a.nav-tab').click(function() {
				var next = jQuery(this);
				var current = next.parent().find('a.nav-tab-active');
				if (current.attr('name') != next.attr('name')) {
					current.removeClass('nav-tab-active');
					jQuery('#'+ current.attr('name')).hide();
					jQuery('#'+ next.attr('name')).show();
					next.addClass('nav-tab-active');
				}
			});
		</script>";
		echo '</div>';
	}
}