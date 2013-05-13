<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午3:37
 *
 */

namespace Query;

/**
 * Disjunction expression implementation
 *
 */
class OrExpr extends \Query\AbstractExpr {

	//Expression
	private $array = array();

	/**
	 * Constructor
	 *
	 * @param array array of disjunctions
	 */
	function __construct($array) {
		if (is_null($array)) throw new \Query\QueryBuilderException("Provided expressions array is null");
		$this->array = $array;
	}

	/**
	 * {@inheritDoc}
	 */
    function __toString() {
        if(0 == count($this->array)) return "";
        $exprString = "";
        foreach($this->array as $e) {
			if (!empty($exprString)) $exprString .= " or ";
			if(is_null($e)) throw new \Query\QueryBuilderException("Provided expression is null, array index: [" + i + "]");
			$exprString .= "({$e})";
		}

        return "({$exprString})";
    }

    /**
	 * {@inheritDoc}
	 */
	function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
		return serialize($this->array) == serialize($o->array);
    }

	function exprs() {
		return $this->array;
	}

}