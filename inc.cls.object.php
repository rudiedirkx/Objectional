<?php

class Object {
	public $__path;
	public $__root;
	public $data = array();
	public $unsaved = false;
	function __construct( $path, $root = false ) {
		$this->__path = realpath($path);
		if ( $root ) {
			$this->__root = $root;
			$this->extend($path);
			$this->init();
		}
		else {
			$this->__root = $this->__path;
		}
	}

	function init() {}

	function asArray() {
		return (array)$this->data;
	}

	function getObjects( $types = array() ) {
		$types = $types ? '['.implode('|', (array)$types).']' : '*';
		$raw = glob($this->dir().'/*.'.$types);
		$objects = array();
		foreach ( $raw AS $obj ) {
			$objects[ self::__id($obj) ] = self::__object($obj, $this->__root);
		}
		return $objects;
	}

	function getObjectIDs( $types = array(), $sort = false ) {
		$types = $types ? '['.implode('|', (array)$types).']' : '*';
		$objects = glob($this->dir().'/*.'.$types);
		$objects = array_map(array(__CLASS__, '__id'), $objects);
		if ( $sort ) {
			natcasesort($objects);
			$objects = array_values($objects);
		}
		return $objects;
	}

	function getObjectByPath( $path ) {
		if ( '/' == substr($path, 0, 1) ) {
			$path = $this->__root . $path;
		}
		else {
			$path = $this->__path.'/'.$path;
		}
		$find = glob($path.'.*');
		if ( $find ) {
			return self::__object($find[0], $this->__root);
		}
		return false;
	}

	function get( $key, $alt = null ) {
		if ( array_key_exists($key, $this->data) ) {
			return $this->data[$key];
		}
		return $alt;
	}

	function set( $key, $value = null, $write = true ) {
		if ( 1 < func_count_args() ) {
			$this->data[$key] = $value;
			$this->unsaved = true;
		}
		else if ( is_object($key) && !is_a($key, __CLASS__) ) {
			$this->data = array_merge($this->data, (array)$key);
			$this->unsaved = true;
		}
		else if ( !is_scalar($key) ) {
			$this->data = array_merge($this->data, (array)$key);
			$this->unsaved = true;
		}
		if ( $write  && $this->unsaved ) {
			$this->write();
			$this->unsaved = false;
		}
		return $this;
	}

	function object( $id ) {
		$id = strtolower($id);
		$dir = $this->dir();
		$object = glob($dir.'/'.str_replace('.', '/', $id).'.*');
		if ( $object ) {
			return new self($object[0], $this->__root);
		}
		return null;
	}

	function create( $id, $type = __CLASS__ ) {
		$type = strtolower($type);
		if ( $this->object($id) ) {
			return false;
		}
		$dir = $this->dir();
		if ( !is_dir($dir) ) {
			mkdir($dir);
		}
		$object = strtolower($id.'.'.__CLASS__);
		$file = $dir.'/'.$object;
		file_put_contents($file, serialize(array()));
		chmod($file, 0777);
		return self::__object($file, $this->__root);
	}

	function parent( $steps = 1 ) {
		$path = $this->__path;
		$steps = max(1, (int)$steps);
		for ( $i=0; $i<$steps; $i++ ) {
			$path = dirname($path);
		}
		if ( strlen($path) < strlen($this->__root) ) {
			return null;
		}
		if ( strlen($path) == strlen($this->__root) ) {
			return new self($path);
		}
		$object = glob($path.'.*');
		if ( $object ) {
			return self::__object($object[0], $this->__root);
		}
		return null;
	}

	function write() {
		return file_put_contents($this->__path, serialize($this->data));
	}

	function __destruct() {
		if ( $this->unsaved ) {
			$this->write();
		}
	}

	function extend( $object ) {
		if ( is_string($object) ) { // filename
			return $this->extend(unserialize(file_get_contents($object)));
		}
		if ( !is_scalar($object) ) {
			$this->data = (array)$object;
#			foreach ( $object AS $k => $v ) {
#				$this->$k = $v;
#			}
		}
		return $this;
	}

	function dir() {
		$p = $this->__path;
		$dir = $this->__root == $p ? $this->__path : dirname($p).'/'.self::__id($p);
		return $dir;
	}

	static function __object($o, $root) {
		list($id, $type) = basename($o);
		$class = class_exists($type) ? $type : __CLASS__;
		return new $class($o, $root);
	}

	static function __id($o) {
		$o = basename($o);
		return substr($o, 0, strpos($o, '.'));
	}

	static function __type($o) {
		$o = basename($o);
		return substr($o, strpos($o, '.')+1);
	}

} // END Class Object


