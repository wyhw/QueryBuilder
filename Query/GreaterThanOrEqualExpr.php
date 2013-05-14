<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-13
 * Time: 下午5:34
 *
 */
namespace Query;

class GreaterThanOrEqualExpr extends \Query\ComparisonExpr
{
	public function mid() {
		return " >= ";
	}

	function evaluate(array $dict) {
		if (parent::evaluate($dict) === false) return false;
		$left = $this->leftVal($dict);
		return is_null($left) ? false : $left >= $this->rightVal($dict);
	}
}