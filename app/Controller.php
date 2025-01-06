<?php
namespace app;

abstract class Controller {
	protected $_context = [];
	protected $_methodAccessPermissions = [];
	
	public function __construct() {}
	
	public function getContext() : Array {
		return $this->_context;
	}
	
	public function getMethodPermissions(string $methodName) : array {
		return isset($this->_methodAccessPermissions[$methodName]) ? $this->_methodAccessPermissions[$methodName] : [];
	}
	
	public function setContext(array $context) : void {
		$this->_context = $context;
	}
	
	public function setMethodsPermissions(array $methodAccessPermissions) : void {
		$this->_methodAccessPermissions = $methodAccessPermissions;
	}
}
