<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-14
 * Time: 上午9:26
 * To change this template use File | Settings | File Templates.
 */

namespace Query;
/**
 * Conjunction expression
 *
 */
class LogicalExpr extends \Query\AbstractExpr {
	function evaluate(array $dict) {
		return true;
	}
}