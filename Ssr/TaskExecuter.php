<?php

class Ssr_TaskExecuter {
	public function __construct() {
		global $wpdb;
		$this->db = new mysqli($wpdb->dbhost, $wpdb->dbuser, $wpdb->dbpassword, $wpdb->dbname);
		$this->db->set_charset($wpdb->charset);
		$this->db->query("SET NAMES '$wpdb->collate'");
	}
	
	/**
	 * 
	 * @param Ssr_Task $task
	 */
	public function execute($task) {
		if ($stmt = $this->db->prepare("UPDATE `{$task->getDbTable()}` SET `{$task->getDbColumn()}` = ? WHERE `{$task->getDbIndex()}` = ?")) {
			$this->num_fails = 0;
			foreach ($task as $k=>$v) {
				$stmt->bind_param('ss', $v, $k);
				if (!$stmt->execute())
					$this->num_fails++;
			}
			$stmt->close();
		}
	}
	
	public function getNumberFails() {
		return $this->num_fails;
	}
	
	private $db;
	private $num_fails;
}