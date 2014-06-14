<?php

/**
 * FlatFile - A DBM database abstraction layer for flat file DB
 * Copyright (c) 2014 Antonio Gallo - http://www.badpenguin.org/
 */

class FlatFile {

	// singleton instances
	private static $instance = array();

	public static function open($database, $readonly=FALSE, $use_cache=TRUE) {
		if (!array_key_exists($database, self::$instance)) {
			// create instance
			self::$instance[$database] = new FlatFileDB($database, $readonly, $use_cache);
		}
		return self::$instance[$database];
	}

	public static function close($database) {
		if (self::$instance[$database]) {
			self::$instance[$database]->close();
		}
		unset(self::$instance[$database]);
	}
}

