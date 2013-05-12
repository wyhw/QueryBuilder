<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午2:45
 *
 */
namespace Query;

/**
 * Expression list implementation, may contain additional prefix,
 * that will be printed only if condition list is not empty
 *
 */
class ExprList implements \Query\ExpressionList {

	const DELIMITER = ", ";

	private $prefix;
	//Expression List
	private $conds = array();

	/**
	 * Constructor
	 *
	 * @param prefix list prefix, will be printed only if
	 * $prefix is String
	 *               condition list is not empty
	 */
	public function __construct($prefix = "") {
		if(is_null($prefix)) throw new \Query\QueryBuilderException("Provided prefix is null");
		$this->prefix = $prefix;
	}

    /**
	 * {@inheritDoc}
	 * @params Expression || String
	 * return ExpressionList
	 */
    function add($expr) {
		if(is_null($expr)) throw new \Query\QueryBuilderException("Provided expression is null");
		if (is_array($expr)) {
			foreach ($expr as $e) $this->conds[] = $e;
			return $this;
		}
		if(is_string($expr)) $expr = new \Query\LiteralExpr($expr);
		$this->conds[] = $expr;
		return $this;
	}

    /**
	 * {@inheritDoc}
	 */
    function __toString() {
        if(empty($this->prefix)) return join($this->conds, self::DELIMITER);
		else if(0 == count($this->conds)) return "";
		else return $this->prefix . " " . join($this->conds, self::DELIMITER);
    }

    /**
	 * {@inheritDoc}
	 * return boolean
	 */
    function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
		return $this->prefix == $o->prefix && serialize($this->conds) == serialize($o->conds);
    }
}
