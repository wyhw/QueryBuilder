<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-13
 * Time: 下午5:34
 *
 */
namespace Query;

class LikeExpr extends \Query\ComparisonExpr
{
	public function mid() {
		return " like ";
	}

	function evaluate(array $dict) {
		if (parent::evaluate($dict) === false) return false;
		$left = $this->leftVal($dict);
		$right = $this->rightVal($dict);
		if ($left == $right) return true;
		if (is_string($left) && is_string($right)) {
			if ($right[0] == '%') $right = substr($right, 1);
			$len = strlen($right);
			if ($right[$len - 1] == '%') $right = substr($right, 0, $len - 1);
			return strpos($left, $right) !== false;
		}
		return false;
	}
}