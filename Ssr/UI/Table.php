<?php

class Ssr_UI_Table {
	public function __construct($style = '') {
		$this->style = $style;
	}
	
	public function setHeader($columns, $repeat=0) {
		$this->header = $columns;
		$this->header_repeat = $repeat;
	}

	public function addRow($row) {
		$this->rows[] = $row;
		$this->max_rowsize = max($this->max_rowsize, sizeof($row));
	}
	
	public function addRows($rows) {
		foreach ($rows as $r)
			$this->addRow($r);
	}
	
	public function sortBy($column, $flags = SORT_ASC) {
		$t = array();
		foreach ($this->rows as $k=>$r)
			$t[$r[$column].$k] = $r;
		if ($flags == SORT_ASC)
			ksort($t);
		else if ($flags == SORT_DESC)
			krsort($t);
		$this->rows = $t;
	}
	
	public function enableRowCount() {
		$this->rowcount = true;
	}
	public function enableColumnCount() {
		$this->colcount = true;
	}
	public function enableSelection($tablename, $actions) {
		$this->selectable = true;
		$this->tablename = $tablename;
		$this->actions = $actions;
	}
	
	public function render() {
		$t = '<table class="'.$this->style.'">';
		
		$th = '<thead>';
		if ($this->colcount) {
			$th .= '<tr>';
			if ($this->rowcount)
				$th .= '<th></th>';
			if ($this->selectable)
				$th .= '<th></th>';
			for ($i=0, $l='A'; $i<$this->max_rowsize; $i++,$l++)
				$th .= "<th>$l</th>";
			$th .= '</tr>';
		}
		if (!empty($this->header)) {
			$th .= '<tr>';
			if ($this->rowcount)
				$th .= '<th></th>';
			if ($this->selectable)
				$th .= '<th></th>';
			foreach ($this->header as $h)
				$th .= "<th>$h</th>";
			$th .= '</tr>';
		}		
		$t .= $th . '</thead>';
		
		$t .= '<tbody>';
		$i = 1;
		foreach ($this->rows as $rc=>$r) {
			if ($this->header_repeat > 0 && $rc>0 && $rc % $this->header_repeat == 0)
				$t .= $th;
			$td = '';
			if ($this->rowcount)
				$td .= "<td>$i</td>";
			if ($this->selectable)
				$td .= '<td><input type="checkbox" name="'.$this->tablename.'" value="true" /></td>';
			foreach ($r as $rd)
				$td .= "<td>$rd</td>";
			$t .= "<tr>$td</tr>";
			$i++;
		}
		$t .= '</tbody></table><p>';
		if (!empty($this->actions)) {
			$t .= $this->actions->render('action_'.$this->tablename);
			$t .= ' <input type="submit" value="Go" class="secondary-button" />';
		}
		return $t.'</p>';
	}

	protected $style = '';
	protected $header = array();
	protected $header_repeat = 0;
	protected $rows = array();
	
	protected $max_rowsize = 0;
	
	protected $rowcount = false;
	protected $colcount = false;
	
	protected $selectable = false;
	protected $tablename = '';
	protected $actions = null;
}