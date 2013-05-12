<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午2:24
 *
 */
namespace Query;

/**
 * Interface for expressions.
 * Expression is printed to output using {@link #toString()} method.
 * All built-in expressions are immutable.
 *
 */
interface Expression {
	/**
	 * Adds another expression to this expression as a conjunction
	 *
	 * @param expr Expression || String
	 * @return Expression
	 */
	function andExpr($expr);
}