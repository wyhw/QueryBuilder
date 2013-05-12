<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午4:10
 *
 */
namespace Query;

/**
 * Interface for expression lists. List is joined from expressions with commas.
 * List is printed to output using {@link #toString()} method.
 *
 */
interface ExpressionList {
	/**
	 * Adds expression to list
	 *
	 * @param expr expression : Expression || String || Array
	 * @return list itself : ExpressionList
	 */
	function add($expr);
}