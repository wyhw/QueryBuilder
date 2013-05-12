<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午2:29
 *
 */
namespace Query;
/**
 * Conjunction expression
 *
 */
class AndExpr extends \Query\AbstractExpr {

	//Left Expression
	private $left;
	//Right Expression
	private $right;

	/**
	 * Constructor
	 *
	 * @param left left expression in conjunction
	 * @param right right expression in conjunction
	 */
	function __construct($left, $right) {
		if(is_null($left)) throw new \Query\QueryBuilderException("Provided left expression is null");
		if(is_null($right)) throw new \Query\QueryBuilderException("Provided right expression is null");
		$this->left = $left;
		$this->right = $right;
	}

	/**
	 * {@inheritDoc}
	 */
    function __toString() {
        return $this->left . " and " . $this->right;
    }

    /**
	 * {@inheritDoc}
	 * return boolean
	 */
    function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
        return $this->left == $o->left && $this->right == $o->right;
    }

}