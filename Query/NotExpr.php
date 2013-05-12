<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午3:31
 *
 */
namespace Query;

/**
 * Negation expression implementation
 *
 */
class NotExpr extends \Query\AbstractExpr {

	//Expression
	private $expr;

	/**
	 * Constructor
	 *
	 * @param expr expression : Expression
	 */
	function __construct($expr) {
		if(is_null($expr)) throw new \Query\QueryBuilderException("Provided expression is null");
		$this->expr = $expr;
	}

	/**
	 * {@inheritDoc}
	 */
    function __toString() {
        return "not (" . $this->expr . ")";
    }

    /**
	 * {@inheritDoc}
	 */
	function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
		return $this->expr == $o->expr;
    }

}