<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-13
 * Time: 下午5:32
 *
 */
namespace Query;

abstract class ComparisonExpr extends \Query\AbstractExpr
{
	protected $left;
	protected $right;

	/**
	 * Comparison Operator constructor.
	 *
	 * @param $left  Left side of comparison
	 * @param $right Right side of comparison
	 */
	public function __construct($left, $right)
	{
		if(is_null($left)) throw new \Query\QueryBuilderException("Provided left expression is null");
		if(is_null($right)) throw new \Query\QueryBuilderException("Provided right expression is null");
		$this->left  = $left;
		$this->right = $right;
	}

	public function mid() {
		return " ";
	}

	public function __toString() {
		$mid = $this->mid();
		if ($this->left instanceof \Query\AbstractExpr || $this->right instanceof \Query\AbstractExpr) {
				$mid = " ";
		}
		$right = $this->right;
		if (!is_numeric($right)) {
			if (!$this->isMysqlField($right)) {
				$right = "'{$right}'";
			}
		}
		return $this->left . $mid . $right ;
	}

	function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
		return $this->left == $o->left && $this->right == $o->right;
	}

	function replaceFieldName($oldName, $newName) {
		if ($oldName == $this->left) $this->left = $newName;
	}

	function evaluate(array $dict) {
		if ($this->left instanceof \Query\AbstractExpr || $this->right instanceof \Query\AbstractExpr) {
			return false;
		}
		return true;
	}

	protected function leftVal($dict) {
		if (!isset($dict["{$this->left}"])) return null;
		return $dict["{$this->left}"];
	}

	protected function rightVal($dict) {
		if (!is_numeric($this->right)) {
			if ($this->isMysqlField($this->right)) {
				$right = substr($this->right, 1, strlen($this->right) - 2);
				if (!empty($right) && isset($dict[$right])) return $dict[$right];
			}
		}
		return $this->right;
	}

	private function isMysqlField($val) {
		return ($val[0] == "`" && $val[strlen($val) - 1] == "`");
	}

}