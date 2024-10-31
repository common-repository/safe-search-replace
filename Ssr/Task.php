<?php

class Ssr_Task implements Countable, Iterator {
	public function __construct($title, $description, $dbTableName, $dbTableIndexColumn, $dbTableColumn, $isundo = false) {
		$this->clear();
		$this->title = $title;
		$this->desc = $description;
		$this->dbtable = $dbTableName;
		$this->dbindex = $dbTableIndexColumn;
		$this->dbcolumn = $dbTableColumn;
		$this->date = date(DATE_ISO8601, time());
		$this->author = wp_get_current_user()->get('user_login');
		$this->isundo = $isundo;
	}
	
	public static function load($file) {
		$f = SSR_PATH_DATA .'/'. $file;
		if (file_exists($f)) {
			$d = unserialize(file_get_contents($f));
			$t = new Ssr_Task($d['title'], $d['description'], $d['database_table'], $d['database_tableindex'], $d['database_tablecolumn'], $d['isundo']);
			$t->date = $d['date'];
			$t->author = $d['author'];
			$t->data = $d['data'];
			$t->file = $file;
			return $t;
		} else 
			return null;
	}
	
	public static function getUndoTask($file) {
		return $file .'.undo';
	}
	
	public static function exists($file) {
		return file_exists(SSR_PATH_DATA .'/'. $file);
	}
	
	public function add($index, $cellValue) {
		$this->data[$index] = $cellValue;
	}
	
	public function save() {
		if (empty($this->file))
			$this->file = time() . ($this->isundo ? '.undo' : '');
		$d = array(
				'title' => $this->title,
				'description' => $this->desc,
				'author' => $this->author,
				'date' => $this->date,
				'database_table' => $this->dbtable,
				'database_tableindex' => $this->dbindex,
				'database_tablecolumn' => $this->dbcolumn,
				'data' => $this->data);
		file_put_contents(SSR_PATH_DATA .'/'. $this->file, serialize($d));
	}
	
	public function clear() {
		$this->data = array();
	}
	
	public function removeKey($key) {
		if (isset($this->data[$key]))
			unset($this->data[$key]);
	}
	
	public function remove() {
		if (!empty($this->file))
			unlink(SSR_PATH_DATA .'/'. $this->file);
		$this->file = '';
	}
	
	public static function getList() {
		$d = scandir(SSR_PATH_DATA);
		rsort($d);
		$l = array();
		foreach ($d as $v)
			if ($v != '.' && $v != '..') {
				$l[(strpos($v, '.undo') !== false ? 'undo' : 'task')][] = $v;
			}
		return $l;
	}
	
	/* Countable */
	public function count() {
		return sizeof($this->data);
	}
	
	/* Iterator */
	function rewind() {
		reset($this->data);
	}
	
	function current() {
		return current($this->data);
	}
	
	function key() {
		return key($this->data);
	}
	
	function next() {
		next($this->data);
	}
	
	function valid() {
		if (current($this->data) == false)
			return false;
		return true;
	}
	
	/* Getter */
	public function getTitle() {
	    return $this->title;
	}

	public function getDesc() {
	    return $this->desc;
	}

	public function getDate()	{
	    return $this->date;
	}

	public function getAuthor() {
	    return $this->author;
	}

	public function getDbTable() {
	    return $this->dbtable;
	}

	public function getDbIndex() {
	    return $this->dbindex;
	}

	public function getDbColumn() {
	    return $this->dbcolumn;
	}
	
	public function isUndo() {
		return $this->isundo;
	}
		

	private $title;
	private $desc;
	private $date;
	private $author;
	private $dbtable;
	private $dbindex;
	private $dbcolumn;
	
	private $isundo = false;	
	private $data;
	
	private $file;
}