<?php
namespace Wisp\Db;
use Wisp\Exception\WispException;
abstract class Object
{
    // ----[ Class Constants ]----------------------------------------
    // CLASS
    const IS_IMPLICIT_ABSTRACT = \ReflectionClass::IS_IMPLICIT_ABSTRACT; // 0x010
    const IS_EXPLICIT_ABSTRACT = \ReflectionClass::IS_EXPLICIT_ABSTRACT; // 0x020
    const IS_FINAL             = \ReflectionClass::IS_FINAL;             // 0x040
   
    // METHOD
    const METHOD_IS_PUBLIC     = \ReflectionMethod::IS_PUBLIC;           // 0x100
    const METHOD_IS_PROTECTED  = \ReflectionMethod::IS_PROTECTED;        // 0x200
    const METHOD_IS_PRIVATE    = \ReflectionMethod::IS_PRIVATE;          // 0x400
    const METHOD_IS_STATIC     = \ReflectionMethod::IS_STATIC;           // 0x001
    const METHOD_IS_ABSTRACT   = \ReflectionMethod::IS_ABSTRACT;         // 0x002
    const METHOD_IS_FINAL      = \ReflectionMethod::IS_FINAL;            // 0x004

    // ----[ Properties ]---------------------------------------------
    /**
     *  @var string
     */
    private $_name   = NULL;

    /**
     *  @var \ReflectionClass
     */
    private $_static = NULL;

    /**
     *  constructor
     */
    public function __construct()
    {
        $this->_static = new \ReflectionClass($this);
        $this->_name   = $this->_static->getName();
    }
  
    public function __call($method, $args)
    {
        if (method_exists($this->_static, $method)) {
            try {
                return call_user_func_array(array($this->_static, $method), $args);
            } catch (WispException $ex) {
                throw new WispException($ex->getMessage(), $ex->getCode());
            }
        }
        $ex_msg = 'Fatal error: Call to undefined method %s::%s()';
        $ex_msg = sprintf($ex_msg, $this->_static->getName(), $method);
        trigger_error($ex_msg, E_USER_ERROR);
        throw new \Exception($ex_msg);
    }
}
