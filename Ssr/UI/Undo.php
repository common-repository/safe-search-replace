<?php

class Ssr_UI_Undo {
	public static function init() {
		add_submenu_page( 'ssr-workspace', 'Undo Recent Tasks', 'Undo', 'publish_posts', 'ssr-undo', array(new Ssr_UI_Undo(), 'display') );
	}
	
	public function display() {
		echo '<div class="wrap">';
		echo '<div id="icon-tools" class="icon32"><br /></div>';
		echo '<h2>Undo Recent Tasks</h2>';
		
		Ssr::loadClass('Ssr_Task');
		
		if (isset($_GET['action']) && isset($_GET['task']) && Ssr_Task::exists($_GET['task'])) {
			if ($_GET['action'] == 'delete') {
				$task = Ssr_Task::load($_GET['task']);
				$task->remove();
				$task = Ssr_Task::load(Ssr_Task::getUndoTask($_GET['task']));
				$task->remove();
				echo '<div class="updated below-h2"><p>Removed Task</p></div>';
			} else if ($_GET['action'] == 'undo') {
				Ssr::loadClass('Ssr_TaskExecuter');
				$undo = new Ssr_TaskExecuter();
				$task = Ssr_Task::load(Ssr_Task::getUndoTask($_GET['task']));
				$undo->execute($task);
				if ($undo->getNumberFails() > 0)
					echo '<div class="updated below-h2"><p>Undone '. $rt->getNumberFails() .'/'. $task->count() .' changes.</p></div>';
				else
					echo '<div class="updated below-h2"><p>Task Undone</p></div>';
				$task->remove();
				$task = Ssr_Task::load($_GET['task']);
				$task->remove();
			}
		}
		
		Ssr::loadClass('Ssr_UI_Table');
		$table = new Ssr_UI_Table('wp-list-table widefat fixed');
		$table->setHeader(array('title'=>'Title', 'desc'=>'Description', 'date'=>'Date Executed', 'author'=>'Author', 'dbtable'=>'Database Table', 'action'=>'Actions'));
		
		
		$tasks = Ssr_Task::getList();
		$canUndo = array();
		if (!empty($tasks)) {
			foreach ($tasks['task'] as $file) {
				$t = Ssr_Task::load($file);
				$action = '';
				if (!isset($dbcolumns[$t->getDbTable().$t->getDbColumn()])) {
					$action = '<a href="?'. http_build_query(array_merge($_GET,array('task'=>$file,'action'=>'undo'))) .'">Undo</a> | ';
					$dbcolumns[$t->getDbTable().$t->getDbColumn()] = $file;
				}
				$action .= '<a href="?'. http_build_query(array_merge($_GET,array('task'=>$file,'action'=>'delete'))) .'" class="delete">Delete</a>';
				$table->addRow( array('title'=>$t->getTitle(), 'desc'=>$t->getDesc(), 'date'=>$t->getDate(),
						 'author'=>$t->getAuthor(), 'dbtable'=>$t->getDbTable().'.'.$t->getDbColumn(), 'action'=>$action) );
			}
			$table->sortBy('date', SORT_DESC);
			echo '<br />';
			echo $table->render();
		} else {
			echo '<div class="updated below-h2"><p>There are no recent tasks</p></div>';
		}
		
		echo '</div>';
	}
}