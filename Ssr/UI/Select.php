<?php

class Ssr_UI_Select {
	public function __construct($name='',$opts=array()) {
		$this->name = $name;
		$this->setGroup('');
		$this->addOptions($opts);
	}
	
	public function setClass($n) {
		$this->class = $n;
		return $this;
	}
	
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	public function addOption($key, $value) {
		$this->opts[$this->state_group]['data'][$key] = $value;
		return $this;
	}
	
	public function addOptions($opts) {
		foreach ($opts as $k=>$v)
			$this->addOption($k, $v);
		return $this;
	}
	
	public function setGroup($name, $label = null) {
		$this->state_group = $name;
		if (!isset($this->opts[$name]['label']))
			$this->opts[$name]['label'] = $label;
		return $this;
	}
	
	public function render($name='', $selected='') {
		if (empty($name))
			$name = $this->name;
		$o = "<select name=\"$name\" id=\"{$this->id}\" class=\"{$this->class}\">";		
		foreach ($this->opts as $g) {
			if (!empty($g['label']))
				$o .= "<optgroup label=\"{$g['label']}\">";
			foreach ($g['data'] as $k=>$v)
				$o .= "<option ".($k==$selected?'selected="selected"':'')." value=\"".htmlspecialchars($k)."\">$v</option>";
			if (!empty($g['label']))
				$o .= '</optgroup>';
		}
		$o .= '</select>';
		return $o;
	}
	
	private $opts = array();
	private $name = '';
	private $class;
	private $id;
	
	private $state_group;
}