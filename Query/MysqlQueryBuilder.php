<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午10:53
 *
 */
namespace Query;

class MysqlQueryBuilder extends \Query\QueryBuilder {
	private $expr;
	private $columns;
	function __construct($q, $columns = null) {
		if (is_string($q)) {
			parent::__construct($q);
		} else {
			if (!is_null($columns)) {
				$this->expr = $q;
				$this->columns = $columns;
			} else {
				parent::__construct(strval($q));
			}
		}
	}
	static function query($q, $columns = null) {
		return new MysqlQueryBuilder($q, $columns);
	}

	public function build() {
		if (is_null($this->expr)) return parent::build();
		$columns = $this->columns;
		foreach ($columns as $key => $val) {
			if ($key != $val) {
				$this->expr->replaceFieldName($key, $val);
			}
		}
		return strval($this->expr);
	}
}