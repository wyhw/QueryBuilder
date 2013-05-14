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
class OrExpr extends \Query\LogicalExpr {

	//Expression
	private $array = array();

	/**
	 * Constructor
	 *
	 * @param array array of disjunctions
	 */
	function __construct() {
		$exprs = array();
		foreach (func_get_args() as $expr) {
			if (is_array($expr)) {
				$exprs = array_merge($exprs, $expr);
				continue;
			}
			if (!($expr instanceof \Query\Expression)) continue;
			$exprs[] = $expr;
		}
		$this->array = $exprs;
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

	function evaluate(array $dict)
	{
		foreach($this->array as $e) {
			if ($e->evaluate($dict)) return true;
		}
		return false;
	}

}