<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午3:26
 *
 */
namespace Query;

/**
 * Literal expression implementation
 *
 */
class LiteralExpr extends \Query\AbstractExpr {

	private $literal;

	/**
	 * Constructor
	 *
	 * @param literal expression literal : String
	 */
	function __construct($literal) {
		if(empty($literal)) throw new \Query\QueryBuilderException("Provided literal is blank");
		$this->literal = $literal;
	}

	/**
	 * {@inheritDoc}
	 */
    function __toString() {
        return strval($this->literal);
    }

    /**
	 * {@inheritDoc}
	 * @return boolean
	 */
    function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
		return $this->literal == $o->literal;
    }

}
