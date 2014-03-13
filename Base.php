<?php
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
*/
class Base {

	private $_errors;
	private $_max_errors;

	function __construct() {
		$this->_errors = array();
		$this->_max_errors = 25;
	}

	function AddError($error) {
		if (!in_array($error, $this->_errors)) {
			$this->_errors[] = $error;
		}
		if (count($this->_errors) > $this->_max_errors) {
			die('Too many errors (limit: ' . $this->_max_errors . '): ' . print_r($this->_errors, 1));
		}
		return true;
	}

	/**
	* Give the errors
	* @return array
	* @access public
	*/
	function GetErrors() {
		return $this->_errors;
	}

	function ResetErrors() {
		$this->_errors = array();
	}
}