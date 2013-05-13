<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午2:19
 *
 */
namespace Query;

abstract class AbstractExpr implements \Query\Expression {

	/**
	 * {@inheritDoc}
	 * $expr : Expression || String
	 * return @Expression
	 */
	function andExpr($expr) {
		if (is_string($expr)) $expr = new \Query\LiteralExpr($expr);
		return new AndExpr($this, $expr);
	}

	function equals($o) {
		return serialize($this) === serialize($o);
	}

	function exprs() {
		return array();
	}

	function replaceFieldName($oldName, $newName) {
		foreach ($this->exprs() as $expr) {
			if (($expr instanceof \Query\ComparisonExpr) || count($expr->exprs()) > 0) {
				$expr->replaceFieldName($oldName, $newName);
			}
		}
	}
}