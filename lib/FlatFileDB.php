<?php

class FlatFileDB {

	const VERSION = 1.01;
	const HANDLER = 'qdbm';

	private $db_handler;
	private $cache = array();
	private $use_cache = TRUE;

	public function __construct($database, $readonly=TRUE, $use_cache=TRUE) {
		$this->use_cache = $use_cache;
		$file_exists = file_exists($database);
		if (!$file_exists) $readonly = FALSE;

		if ($readonly) {
			$opt = 'rl';

			if (!is_readable($database)) {
				 throw new Exception('database is not readable: '.$database);
				 return FALSE;
			}

		} else {
			$opt = 'cl';

			if ($file_exists) {
				if (!is_writable($database)) {
					 throw new Exception('database is not writeable: '.$database);
					 return FALSE;
				}
			} else {
				if (!is_writable(dirname($database))) {
					 throw new Exception('database is not inside a writeable directory: '.$database);
					 return FALSE;
				}
			}
		}
		$this->db_handler = dba_open($database,$opt,FlatFileDB::HANDLER);
		if (!$this->db_handler) {
			 throw new Exception('cannot open database: '.$database);
			 return FALSE;
		}

		return $this;
	}


	public function close() {
		dba_close($this->db_handler);
		$this->db_handler = FALSE;
		$this->cache = array();
	}

	public function get($key) {
		if ($this->use_cache && array_key_exists($key,$this->cache)) {
			return $this->cache[$key];
		}
		$v = dba_fetch($key,$this->db_handler);

		// convert
		$value = $this->decode($v);

		// store
		if ($this->use_cache) 
			$this->cache[$key]=$value;
		return $value;
	}

	public function set($key,$value) {
		// Store
		if ($this->use_cache) $this->cache[$key]=$value;

		// Convert
		$v = $this->encode($value);

		// Write
		if (dba_exists($key,$this->db_handler)) {
			$r = dba_replace( $key, $v, $this->db_handler );
		} else {
			$r = dba_insert( $key, $v, $this->db_handler );
		}

		return $r;
	}

	public function delete($key) {
		//if (array_key_exists($key,$this->cache)) {
		unset($this->cache[$key]);
		return dba_delete($key,$this->db_handler);
	}

	public function flush() {
		$this->cache = array();
		return dba_sync($this->db_handler);
	}

	public function get_all() {
		// reset cache
		$this->cache = array();
		$tmp = array();
		// read all
		for (
			$key = dba_firstkey($this->db_handler); 
			$key != false; 
			$key = dba_nextkey($this->db_handler)
		) {
			$v = dba_fetch($key, $this->db_handler);
			// Convert
			$value = $this->decode($v);
			// Store
			$tmp[$key]=$value;
		}
		if ($this->use_cache) $this->cache = $tmp;
		return $tmp;
	}

	public function is_valid($key) {
		return dba_exists($key,$this->db_handler);
	}

	public function optimize() {
		$this->cache = array();
		return dba_optimize($this->db_handler);
	}

	public function encode($v) {
		return json_encode($v);
	}
	public function decode($v) {
		return json_decode($v);
	}

	public function debug() {
		echo "Available DBA handlers:\n<ul>\n";
		foreach (dba_handlers(true) as $handler_name => $handler_version) {
			// clean the versions
			$handler_version = str_replace('$', '', $handler_version);
			echo "<li>$handler_name: $handler_version</li>\n";
		}
		echo "</ul>\n";

		echo "All opened databases:\n<ul>\n";
		foreach (dba_list() as $res_id=>$db_name) {
			// clean the versions
			echo "<li>$res_id : $db_name</li>\n";
		}
		echo "</ul>\n";
	}

	public function get_cache() { return $this->cache; }

}
