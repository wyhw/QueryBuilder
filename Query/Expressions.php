<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午3:15
 *
 */

namespace Query;

/**
 * Static helper methods to work with expressions and expression lists
 *
 * @author alexkasko
 * Date: 11/7/12
 */
final class Expressions {

	function __construct() { }

    /**
	 * Creates expression list
	 *
	 * @return ExpressionList expression list
	 */
    public static function exprList() {
		return new \Query\ExprList();
	}

    /**
	 * Creates expression list with prefix literal,
	 * that will be printed only if condition list is not empty
	 *
	 * @param prefix list prefix literal
	 * @return ExpressionList expression list
	 */
	public static function  listWithPrefix($prefix) {
		return new \Query\ExprList($prefix);
	}

    /**
	 * Creates expression list with {@code "order by"} prefix,
	 * that will be printed only if condition list is not empty
	 *
	 * @return ExpressionList expression list
	 */
    public static function orderBy() {
        return new \Query\ExprList("order by");
    }

    /**
	 * Creates expression from string literal
	 *
	 * @param expr expression literal : String
	 * @return expression
	 * @throws QueryBuilderException on empty input
	 */
    public static function expr($expr) {
		if ($expr instanceof \Query\AbstractExpr) return $expr;
		return new \Query\LiteralExpr($expr);
	}

    /**
	 * Creates negation expression for given expression
	 *
	 * @param expr expression : Expression || String
	 * @return negation expression
	 * @throws QueryBuilderException on null input
	 */
    public static function not($expr) {
		if (is_string($expr)) $expr = new \Query\LiteralExpr($expr);
		return new \Query\NotExpr($expr);
	}

    /**
	 * Creates disjunction expression for expressions
	 *
	 * @param exprs expressions for disjunction
	 * @return disjunction expression
	 * @throws QueryBuilderException on null input
	 */
    public static function orExpr() {
		$exprs = array();
		foreach (func_get_args() as $expr) {
			if (!($expr instanceof \Query\Expression)) continue;
			$exprs[] = $expr;
		}
		return new \Query\OrExpr($exprs);
	}

    /**
	 * Creates prefix expression, prefix will be printed
	 * only if this expression will be conjuncted with other expressions,
	 * empty string will be printed otherwise
	 *
	 * @param prefix prefix literal : String
	 * @return prefix expression
	 * @throws QueryBuilderException on empty input
	 */
    public static function prefix($prefix) {
		return  new \Query\PrefixExpr($prefix);
	}

    /**
	 * Creates prefix expression with {@code "where"} prefix, that
	 * will be printed only if this expression will be conjuncted with other expressions,
	 * empty string will be printed otherwise
	 *
	 * @return prefix expression with {@code "where"} prefix
	 */
    public static function where() {
        return new \Query\PrefixExpr("where");
    }

    /**
	 * Creates prefix expression with {@code "and"} prefix, that
	 * will be printed only if this expression will be conjuncted with other expressions,
	 * empty string will be printed otherwise
	 *
	 * @return prefix expression with {@code "and"} prefix
	 */
    public static function andExpr() {
		return new \Query\PrefixExpr("and");
	}
}
