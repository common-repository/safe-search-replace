<?php

class Ssr_UI_Shortcode {
	public static function init() {
		add_submenu_page( 'ssr-workspace', 'Shortcodes', 'Shortcodes', 'publish_posts', 'ssr-shortcodes', array(new Ssr_UI_Shortcode(), 'display') );
	}
	
	public function display() {
		echo '<div class="wrap">';
		echo '<div id="icon-tools" class="icon32"><br /></div>';
		echo '<h2>Change Shortcodes</h2>';
			
		Ssr::loadClass('Ssr_UI_Select');
		Ssr::loadClass('Ssr_UI_TableSettings');
		
		$optsWhere = array('post_content'=>'Post Contents','post_excerpt'=>'Post Excerpts', 'comments'=>'Comments');
		$optsTasks = array('rename'=>'Rename Shortcodes', 'delete'=>'Delete Shortcodes', 'edit'=>'Edit Shortcodes (+ Rename Shortcode)');
		
		$m = '';
		if (!empty($_POST))
			$_POST = stripslashes_deep($_POST);
		if (isset($_POST['apply']) && ($m=$this->prepareArguments($_POST)) === true) {			
			$this->prepareShortcodeCallbacks();
			$loc = $this->resolveLocation($_POST);
			global $wpdb;
			$res = $wpdb->get_results("SELECT `{$loc['dbcolumn']}`,`{$loc['dbindex']}` FROM `{$loc['dbtable']}` WHERE `{$loc['dbcolumn']}` LIKE '%[{$_POST['shortcode']} %'");
			if (!empty($res)) {
				Ssr::loadClass('Ssr_Task');
				Ssr::loadClass('Ssr_TaskExecuter');
				
				$title = 'Shortcodes: ';
				$desc = 'Number of changes: '. sizeof($res) .'.';
				switch ($_POST['action']) {
					case 'rename': 
						$title .= 'Renamed "'. $_POST['shortcode'] .'" into "'. $_POST['shortcode_new'] .'"';
						$desc .= " Shortcode tag '{$_POST['shortcode']}' has been renamed in ".sizeof($res)." {$optsWhere[$_POST['where']]}";
						break;
					case 'delete':  
						$title .= 'Removed "'. $_POST['shortcode'] .'"'; 
						$desc .= " Shortcode tag '{$_POST['shortcode']}' has been removed from ".sizeof($res)." {$optsWhere[$_POST['where']]}";
						break;
					case 'edit':  
						$title .= 'Changed "'. $_POST['shortcode'] .'"'; 
						$desc .= " Shortcode tag '{$_POST['shortcode']}' has been changed in ".sizeof($res)." {$optsWhere[$_POST['where']]}.";
						if ($_POST['shortcode_new'] != $_POST['shortcode'])
							$desc .= " New Tag: '{$_POST['shortcode_new']}'.";
						if (!empty($_POST['remove_attr']))
							$desc .= " Removed attributes: '{$_POST['remove_attr']}'.";
						if (!empty($_POST['rename_attr']))
							$desc .= " Renamed attributes: '{$_POST['rename_attr']}'.";
						if (!empty($_POST['add_attr']))
							$desc .= " Added attributes: '{$_POST['add_attr']}'.";
						$desc .= " Empty shortcodes have been ". ($_POST['empty_shortcodes']=='preserve' ? 'preserved' : 'removed') .".";
						break;
				}
				$task = new Ssr_Task($title, $desc, $loc['dbtable'], $loc['dbindex'], $loc['dbcolumn']);
				$taskUndo = new Ssr_Task('(Undo) '. $title, $desc, $loc['dbtable'], $loc['dbindex'], $loc['dbcolumn'], true);
				
				$count = 0;
				foreach ($res as $r) {
					$new = do_shortcode($r->$loc['dbcolumn']);
					if ($new != $r->$loc['dbcolumn']) {
						$taskUndo->add($r->$loc['dbindex'], $r->$loc['dbcolumn']);
						$task->add($r->$loc['dbindex'], $new);
						$count++;
					}
				}
				echo '<p>Found '. $count .' Results</p>';
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
				echo '<div class="updated below-h2"><p>Nothing found</p></div>';
		} else {
			if (!empty($m))
				echo '<div class="updated below-h2"><p>'. $m .'</p></div>';
			
			$tasks = new Ssr_UI_Select();
			$tasks->addOptions($optsTasks);
			$where = new Ssr_UI_Select();
			$where->addOptions($optsWhere);
			$emptytags = new Ssr_UI_Select();
			$emptytags->addOptions(array('preserve'=>'Preserve', 'remove'=>'Remove'));
			
			echo '<form action="" method="post" id="searchargs">';
			
			$table = new Ssr_UI_TableSettings();
			$table->addRow(array('Shortcode Name', '<input type="text" name="shortcode" />'));
			$table->addRow(array('What To Do', $tasks->render('action') .' (Settings see below)'));
			$table->addRow(array('Where To Search', $where->render('where')));		
			echo $table->render();
			
			echo '<p>&nbsp;</p><h3>Rename Shortcodes</h3>';
			$table = new Ssr_UI_TableSettings();
			$table->addRow(array('New Shortcode Name', '<input type="text" name="shortcode_new" />'));
			echo $table->render();
			
			echo '<p>&nbsp;</p><h3>Edit Shortcodes</h3>';
			$table = new Ssr_UI_TableSettings();
			$table->addRow(array('Remove Attributes', '<input type="text" name="remove_attr" /> (e.g.: key1 key2="val2" key3="val3")'));
			$table->addRow(array('Rename Attributes', '<input type="text" name="rename_attr" /> (e.g.: oldkey="newkey" oldkey2="newkey2")'));
			$table->addRow(array('Add Attributes', '<input type="text" name="add_attr" /> (e.g.: newkey1 newkey2="newval2" existingkey="!overwrite old value 3")'));
			$table->addRow(array('Empty Shortcodes', $emptytags->render('empty_shortcodes') ));
			echo $table->render();
			
			echo '<input type="submit" name="apply" value="Apply Changes" class="button-primary" />';
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
					'class': 'Ssr_UI_Shortcode',
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
		if (!empty($args['shortcode'])) {
			if ( ($m=$this->prepareArguments($args)) === true) {
				$this->prepareShortcodeCallbacks();
				$loc = $this->resolveLocation($args);
				global $wpdb;
				$res = $wpdb->get_results("SELECT `{$loc['dbcolumn']}`,`{$loc['dbindex']}` FROM `{$loc['dbtable']}` WHERE `{$loc['dbcolumn']}` LIKE '%[{$args['shortcode']} %'");
				if (!empty($res)) {
					$count = 0;
					$t = '<ol>';
					foreach ($res as $r) {
						$new = do_shortcode($r->$loc['dbcolumn']);
						if ($new != $r->$loc['dbcolumn']) {
							$t .= "<li>$new</li>";
							$count++;
						}
					}
					echo '<div class="updated below-h2"><p><strong>'. $count .' Results</strong></p></div>';
					echo $t;
					echo '</ol>';
				} else
					echo '<div class="updated below-h2"><p>Nothing found</p></div>';
			} else {
				echo '<div class="updated below-h2"><p>'. $m .'</p></div>';
			}
		} else {
			echo '<div class="updated below-h2"><p>Which shortcode do you want to remove, rename or edit?</p></div>';
		}
	}
	
	private function prepareArguments($args) {
		$this->args['remove'] = array();
		$this->args['rename'] = array();
		$this->args['add'] = array();
		
		if (empty($args['shortcode']))
			return 'Which shortcode do you want to remove, rename or edit?';
		if (($args['action'] == 'rename' || $args['action'] == 'edit') && empty($args['shortcode_new']))
			return 'What should be the new shortcode name?';
		if ($args['action'] == 'edit') {
			if (empty($args['remove_attr']) && empty($args['rename_attr']) && empty($args['add_attr']))
				return 'How do you want to edit the shortcode?';
			$this->args['remove'] = shortcode_parse_atts($args['remove_attr']);
			$this->args['rename'] = shortcode_parse_atts($args['rename_attr']);
			$this->args['add'] = shortcode_parse_atts($args['add_attr']);
			if (empty($this->args['remove']))
				$this->args['remove'] = array();
			if (empty($this->args['rename']))
				$this->args['rename'] = array();
			if (empty($this->args['add']))
				$this->args['add'] = array();
		}
		if ($args['action'] == 'delete') {
			$args['shortcode_new'] = '';
		}
		$this->args['action'] = $args['action'];
		$this->args['shortcode'] = $args['shortcode'];
		$this->args['newshortcode'] = $args['shortcode_new'];
		$this->args['preserve'] = $args['empty_shortcodes'] == 'preserve';
		return true;
	}
		
	
	private function prepareShortcodeCallbacks() {
		remove_all_shortcodes();
		add_shortcode($this->args['shortcode'], array($this, 'doShortcode'));
	}
	
	public function doShortcode($atts, $content) {
		if (!empty($content))
			$content = do_shortcode($content);
		foreach ($this->args['remove'] as $k=>$v) {
			if (is_int($k) && isset($atts[$v]))
				unset($atts[$v]);
			else if (is_string($k) && isset($atts[$k]) && $atts[$k] == $v)
				unset($atts[$k]);
		}
		foreach ($this->args['rename'] as $k1=>$k2) {
			if (is_string($k1) && is_string($k2) && isset($atts[$k1])) {
				$atts[$k2] = $atts[$k1];
				unset($atts[$k1]);
			}
		}
		foreach ($this->args['add'] as $k=>$v) {
			if (is_int($k)) 
				$atts[] = $v;
			else if (!empty($v) && $v[0] == '!') {
				$atts[$k] = substr($v, 1);
			} else if (!isset($atts[$k]))
				$atts[$k] = $v;
		}
		return $this->buildShortcode($atts, $content);
	}
	
	private function buildShortcode($atts, $content) {
		$words = array();
		foreach ($atts as $k=>$v) {
			if (is_int($k)) {
				unset($atts[$k]);
				$words[] = $v;
			}
		}
		$atts = array_merge($atts, array_unique($words));
		
		if (empty($this->args['newshortcode']) ||  (empty($atts) && empty($content) && !$this->args['preserve']))
			return '';		
		$s = "[{$this->args['newshortcode']}";
		foreach ($atts as $k=>$v) {
			if (is_int($k))
				$s .= " $v";
			else 
				$s .= " $k=\"$v\"";
		}
		if (!empty($content))
			$s .= " ]{$content}[/{$this->args['newshortcode']}]";
		else
			$s .= " /]";
		return $s;
	}
	
	private function resolveLocation($args) {
		global $wpdb;
		$w = array();
		switch ($args['where']) {
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
	
	private $args;
}