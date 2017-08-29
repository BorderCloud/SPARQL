<?php
declare(strict_types=1);
/**
 * @git git@github.com:BorderCloud/SPARQL.git
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/
 */
namespace BorderCloud\SPARQL;

/**
 * Class Base
 * TODO refresh doc
 *
 * @package BorderCloud\SPARQL
 */
class Base
{

    /**
     * TODO
     *
     * @var array
     */
    private $_errors;

    /**
     * TODO
     *
     * @var int
     */
    private $_maxErrors;

    /**
     * TODO
     *
     * Base constructor.
     */
    public function __construct()
    {
        $this->_errors = array();
        $this->_maxErrors = 25;
    }

    /**
     * TODO
     *
     * @param $error
     * @return bool
     */
    public function addError($error)
    {
        if (! in_array($error, $this->_errors)) {
            $this->_errors[] = $error;
        }
        if (count($this->_errors) > $this->_maxErrors) {
            die('Too many errors (limit: ' . $this->_maxErrors . '): ' . print_r($this->_errors, true));
        }
        return true;
    }

    /**
     * Give the errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * TODO
     *
     */
    public function resetErrors()
    {
        $this->_errors = array();
    }
}
