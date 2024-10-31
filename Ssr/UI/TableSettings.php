<?php

Ssr::loadClass('Ssr_UI_Table');

class Ssr_UI_TableSettings extends Ssr_UI_Table {
	public function __construct() {
		parent::__construct('form-table');
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
		$i = 1; $ir = 0;
		foreach ($this->rows as $rc=>$r) {
			if ($this->header_repeat > 0 && $rc>0 && $rc % $this->header_repeat == 0)
				$t .= $th;
			$td = '';
			if ($this->rowcount)
				$td .= "<td>$i</td>";
			if ($this->selectable)
				$td .= '<td><input type="checkbox" name="'.$this->tablename.'" value="true" /></td>';
			foreach ($r as $rd) {
				if ($ir==0)
					$td .= "<th>$rd</th>";
				else
					$td .= "<td>$rd</td>";
				$ir++;
			}
			$t .= "<tr>$td</tr>";
			$i++; $ir = 0;
		}
		$t .= '</tbody></table><p>';
		if (!empty($this->actions)) {
			$t .= $this->actions->render('action_'.$this->tablename);
			$t .= ' <input type="submit" value="Go" class="secondary-button" />';
		}
		return $t.'</p>';
	}
}