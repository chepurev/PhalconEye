<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to lantian.ivan@gmail.com so we can send you a copy immediately.
 *
 */

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 */

class Validator_Regex extends Validator_Abstract
{
    const INVALID   = 'regexInvalid';
    const NOT_MATCH = 'regexNotMatch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given, value should be string, integer or float",
        self::NOT_MATCH => "'%value%' does not match against pattern '%pattern%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'pattern' => '_pattern'
    );

    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern;

    /**
     * Sets validator options
     *
     * @param  string $pattern
     * @return void
     */
    public function __construct($pattern)
    {
        $this->setPattern($pattern);
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * Sets the pattern option
     *
     * @param  string $pattern
     * @return Validator_Regex Provides a fluent interface
     */
    public function setPattern($pattern)
    {
        $this->_pattern = (string) $pattern;
        return $this;
    }

    /**
     * Defined by Validator_Interface
     *
     * Returns true if and only if $value matches against the pattern option
     *
     * @param  string $value
     * @throws Validator_Exception if there is a fatal error in pattern matching
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        $status = @preg_match($this->_pattern, $value);
        if (false === $status) {
            // require_once 'Zend/Validate/Exception.php';
            throw new Validator_Exception("Internal error matching pattern '$this->_pattern' against value '$value'");
        }
        if (!$status) {
            $this->_error(self::NOT_MATCH);
            return false;
        }
        return true;
    }

}
