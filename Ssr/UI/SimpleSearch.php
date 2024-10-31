<?php

class Ssr_UI_SimpleSearch {
	public static function init() {
		add_submenu_page( 'ssr-workspace', 'Simple Search', 'Simple Search', 'publish_posts', 'ssr-simplesearch', array(new Ssr_UI_SimpleSearch(), 'display') );
	}
	
	public function display() {
		Ssr::loadClass('Ssr_UI_Select');
		Ssr::loadClass('Ssr_UI_TableSettings');
		
		echo '<div class="wrap">';
		echo '<div id="icon-tools" class="icon32"><br /></div>';
		echo '<h2>Simple Search</h2>';
		
		$optsWhere = array('post_title'=>'Post Title','post_content'=>'Post Content','post_excerpt'=>'Post Excerpts', 'comments'=>'Comments');
		
		if (isset($_POST['replaceall']) && !empty($_POST['search'])) {
			$loc = $this->resolveLocation($_POST);
			global $wpdb;
			$res = $wpdb->get_results("SELECT `{$loc['dbcolumn']}`,`{$loc['dbindex']}` FROM `{$loc['dbtable']}` WHERE `{$loc['dbcolumn']}` LIKE '%{$_POST['search']}%'");
			if (!empty($res)) {
				Ssr::loadClass('Ssr_Task');
				Ssr::loadClass('Ssr_TaskExecuter');
				
				echo '<p>Found '. sizeof($res) .' results</p>';
				$task = new Ssr_Task("SimpleSearch: replaced '{$_POST['search']}' width '{$_POST['replace']}'", 
								'Updated '. sizeof($res) .' results. Searched in '. $optsWhere[$_POST['where']] .'.', 
								$loc['dbtable'], $loc['dbindex'], $loc['dbcolumn']);
				$taskUndo = new Ssr_Task("(Undo) SimpleSearch: replaced '{$_POST['search']}' width '{$_POST['replace']}'", 'Updated {'. sizeof($res) .'} results.',
								$loc['dbtable'], $loc['dbindex'], $loc['dbcolumn'], true);
				foreach ($res as $r) {
					if ($_POST['searchcase'] != 'sensitive' || strpos($r->$loc['dbcolumn'], $_POST['search']) !== false) {
						$taskUndo->add($r->$loc['dbindex'], $r->$loc['dbcolumn']);
						$task->add($r->$loc['dbindex'], str_ireplace($_POST['search'], $_POST['replace'], $r->$loc['dbcolumn']));	
					}
				}
				$taskUndo->save();
				$task->save();
				echo '<p>Successfully created task with undo option</p>';
				$rt = new Ssr_TaskExecuter();
				$rt->execute($task);
				if ($rt->getNumberFails() > 0)
					echo '<p>Executed '. $rt->getNumberFails() .'/'. $task->count() .' changes.</p>';
				else
					echo '<p>Successfully executed task</p>';
			} else
				echo '<p>Nothing found</p>';
		} else {
			$where = new Ssr_UI_Select();
			$where->addOptions($optsWhere);
			
			$sensitive = new Ssr_UI_Select();
			$sensitive->addOptions(array('sensitive'=>'Yes, respect small and BIG letters', 'insensitive'=>'No, it doesn\'t matter'));
			
			$table = new Ssr_UI_TableSettings();
			$table->addRow(array('Where To Search', $where->render('where')));
			$table->addRow(array('Search', '<input type="text" name="search" value="" />'));
			$table->addRow(array('Replace With', '<input type="text" name="replace" value="" />'));
			$table->addRow(array('Case sensitive', $sensitive->render('searchcase')));
			
			echo '<form action="" method="post" id="searchargs">';
			echo $table->render();
			echo '<input type="submit" name="replaceall" value="Replace All" class="button-primary" />';
			echo '<input type="button" name="preview" value="Preview" class="button-secondary" />';
			echo '</form>';
		}
		
		echo '<br /><br /><div id="preview"></div>';
		echo '</div>';
		
?>
<script>
jQuery(document).ready(function(e) {
	jQuery('input[name="preview"]').click(function(e) {
		var data = {
			action: 'safesearchreplace',
			'class': 'Ssr_UI_SimpleSearch',
			'method': 'preview',
			'search': jQuery('#searchargs').serialize()
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#preview').html(response);
		});
		
	});
});
</script>
<?php 
	}
	
	public function preview($args) {
		parse_str($args['search'], $args);
		if (!empty($args['search'])) {
			$loc = $this->resolveLocation($args);
			global $wpdb;
			$res = $wpdb->get_results("SELECT `{$loc['dbcolumn']}`,`{$loc['dbindex']}` FROM `{$loc['dbtable']}` WHERE `{$loc['dbcolumn']}` LIKE '%{$args['search']}%'");
			if (!empty($res)) {
				$count = 0;
				$t = '<ol>';
				foreach ($res as $r) {
					if ($args['searchcase'] != 'sensitive' || strpos($r->$loc['dbcolumn'], $args['search']) !== false) {
						$t .= '<li>';
						$t .= str_ireplace($args['search'], "<span style=\"color:red;text-decoration:line-through\">{$args['search']}</span><span style=\"color:green\">{$args['replace']}</span>", $r->$loc['dbcolumn']);
						$t .= '</li>';
						$count++;
					}
				}
				echo '<div class="updated below-h2"><p><strong>'. $count .' Results</strong></p></div>';
				echo $t;
				echo '</ol>';
			} else 
				echo '<div class="updated below-h2"><p>Nothing found</p></div>';
		} else {
			echo '<div class="updated below-h2"><p>What do you want to search for?</p></div>';
		}
	}
	
	private function resolveLocation($args) {
		global $wpdb;
		$w = array();
		switch ($args['where']) {
			case 'post_title':
				$w['dbtable'] = $wpdb->posts;
				$w['dbcolumn'] = 'post_title';
				$w['dbindex'] = 'ID';
				break;
			case 'post_content':
				$w['dbtable'] = $wpdb->posts;
				$w['dbcolumn'] = 'post_content';
				$w['dbindex'] = 'ID';
				break;
			case 'post_excerpt':
				$w['dbtable'] = $wpdb->posts;
				$w['dbcolumn'] = 'post_excerpt';
				$w['dbindex'] = 'ID';
				break;
			case 'comments':
				$w['dbtable'] = $wpdb->comments;
				$w['dbcolumn'] = 'comment_content';
				$w['dbindex'] = 'comment_ID';
				break;
		}
		return $w;
	}
}