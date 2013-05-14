<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-13
 * Time: 下午5:34
 *
 */
namespace Query;

class NotEqualExpr extends \Query\ComparisonExpr
{
	public function mid() {
		return " != ";
	}

	function evaluate(array $dict) {
		if ($this->left instanceof \Query\AbstractExpr) {
			if ($this->right instanceof \Query\AbstractExpr) {
				return $this->left->evaluate($dict) !== $this->right->evaluate($dict);
			} else {
				return $this->left->evaluate($dict) !== $this->right;
			}
		}
		$left = $this->leftVal($dict);
		if ($this->right instanceof \Query\AbstractExpr) {
			return $left !== $this->right->evaluate($dict);
		}
		return $left != $this->rightVal($dict);
	}
}