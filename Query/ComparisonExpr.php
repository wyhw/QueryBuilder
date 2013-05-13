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
	 * @param Variable $left  Left side of comparison
	 * @param Variable $right Right side of comparison
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
		$right = $this->right;
		if (!is_numeric($right)) {
			if (!($right[0] == "`" && $right[strlen($right) - 1] == "`")) {
				$right = "'{$right}'";
			}
		}
		return $this->left . $this->mid() . $right ;
	}

	function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
		return $this->left == $o->left && $this->right == $o->right;
	}

	function replaceFieldName($oldName, $newName) {
		if ($oldName == $this->left) $this->left = $newName;
	}
}