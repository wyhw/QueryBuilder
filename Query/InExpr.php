<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-13
 * Time: 下午5:34
 *
 */
namespace Query;

class InExpr extends \Query\ComparisonExpr
{
	public function mid() {
		return " in (";
	}

	public function __toString() {
		if (is_array($this->right)) {
			if (count($this->right) == 0) throw new \Query\QueryBuilderException("Provided right expression is empty");
			$right = "";
			foreach ($this->right as $r) {
				if ($right != "") $right .= ",";
				if (is_numeric($r)) $right .= $r;
				else $right .= "'{$r}'";
			}
			return $this->left . $this->mid() . $right . ")" ;
		}
		return $this->left . $this->mid() . $this->right . ")" ;
	}

	function evaluate(array $dict) {
		$right = $this->right;
		if (!is_array($right)) $right = explode(",", $right);

		if (count($right) == 0) return false;
		$left = $this->leftVal($dict);
		foreach ($right as $r) {
			if (is_string($r)) $r = trim($r);
			if ($left == $r) return true;
		}
		return false;
	}
}