<?php

class Object {
	public $__path;
	public $__root;
	public $data = array();
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

	function init() { }

	function getObjects() {
		$raw = glob($this->dir().'/*.*');
		$objects = array();
		foreach ( $raw AS $obj ) {
			$objects[ self::__id($obj) ] = self::__object($obj, $this->__root);
		}
		return $objects;
	}

	function getObjectIDs() {
		$objects = glob($this->dir().'/*.*');
		$objects = array_map(array(__CLASS__, '__id'), $objects);
		return $objects;
	}

	function get( $k ) {
		$k = strtolower($k);
		if ( array_key_exists($k, $this->data) ) {
			return $this->data[$k];
		}
		$dir = $this->dir();
		$object = glob($dir.'/'.str_replace('.', '/', $k).'.*');
		if ( $object ) {
			return new self($object[0], $this->__root);
		}
		return false;
	}

	function create( $id, $type = __CLASS__ ) {
		$type = __CLASS__;
		$type = strtolower($type);
#		if ( glob($this->__path.'/'.strtolower($id).'.*') ) {
		if ( $this->get($id) ) {
			return false;
		}
		$dir = $this->dir();
		if ( !is_dir($dir) ) {
			mkdir($dir);
		}
		$object = strtolower($id.'.'.__CLASS__);
		file_put_contents($dir.'/'.$object, serialize(array()));
		return self::__object($dir.'/'.$object, $this->__root);
	}

	function parent( $steps = 1 ) {
		
	}

	function extend( $object ) {
		if ( is_string($object) ) { // filename
			return $this->extend(unserialize(file_get_contents($object)));
		}
		if ( !is_scalar($object) ) {
			$this->data = $object;
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


