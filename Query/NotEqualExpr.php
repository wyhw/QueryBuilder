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
}