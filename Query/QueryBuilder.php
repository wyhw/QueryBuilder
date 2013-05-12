<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午3:59
 *
 */
namespace Query;

/**
 * Builder class for string queries. Query is build from template - string with placeholder sections (clauses),
 * and expressions (lists) set to to builder to fill clauses.
 * Placeholder syntax: {@code ${placeholder_42}
 * Placeholder escaping syntax: {@code $${placeholder_42} (will NOT be parsed)
 * Placeholder name must conform this regex: {@code [a-zA-Z_0-9]+}
 * "select emp.* from employee emp
 *   join departments dep on emp.id_department = dep.id
 *   @{where}
 *   @{order}
 *   limit :limit offset :offset";
 */
class QueryBuilder {
	const CLAUSE_PATTERN = '/[^@]@\{(\w+)\}/';

	private $template;
	private $clauses = array();

	/**
	 * Constructor
	 *
	 * @param template query template
	 */
	function __construct($template) {
		if(empty($template)) throw new \Query\QueryBuilderException("Provided template is blank");
		$this->template = $template;
		if (preg_match_all(self::CLAUSE_PATTERN, $template, $matches)) {
			foreach($matches[1] as $key) {
				$this->clauses[$key] = null;
			}
		}
	}

	/**
	 * Static method, invokes constructor
	 *
	 * @param template query template
	 * @return query vuilder instance
	 */
	static function query($template) {
		return new QueryBuilder($template);
	}

    /**
	 * Registers provided string for clause
	 *
	 * @param clauseName clause name
	 * @param value clause value : Expression || String || ExprList
	 * @return builder itself
	 */
    public function set($clauseName, $expr) {
		if (empty($clauseName)) throw new \Query\QueryBuilderException("Provided clauseName is blank");
		if (is_string($expr)) $expr = new \Query\LiteralExpr($expr);
		if (!is_array($expr)) {
			$exprList = new \Query\ExprList();
			$expr = $exprList->add($expr);
		}
		if(!array_key_exists($clauseName, $this->clauses)) throw new \Query\QueryBuilderException(
			"Provided clauseName: [" . $clauseName . "] is not found in the template: [" . $this->template . "]" .
				" registered clauses: [" . $this->clauses . "]" .
				" (clause name must conform this regex: '[a-zA-Z_0-9]+')");
		if(!is_null($this->clauses[$clauseName])) throw new \Query\QueryBuilderException(
			"Provided clauseName: [" . $clauseName . "] was already set to: [" . $this->clauses[$clauseName] . "]");
		if(is_null($expr)) throw new \Query\QueryBuilderException("Provided expr is null");
		$this->clauses[$clauseName] = $expr;
		return $this;
	}

    /**
	 * Builds query string from template and accumulated values
	 *
	 * @return query string
	 * @throws IllegalStateException on not filled clause
	 */
    public function build() {
		$replacePairs = array();
		foreach ($this->clauses as $key => $expr) {
			if (is_null($expr)) throw new QueryBuilderException(
				"Clause: [" . $key . "] wasn't filled, template: [" . $this->template . "]");
			$replacePairs["@{{$key}}"] = strval($expr);
		}
		return strtr($this->template, $replacePairs);
    }
}