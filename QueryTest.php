<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午4:56
 *
 */
spl_autoload_register(function ($name) {
	$name = strtr($name, '\\', DIRECTORY_SEPARATOR);
	if (file_exists("{$name}.php")) {
		require_once "{$name}.php";
	}
});
class QueryTest extends \PHPUnit_Framework_TestCase {
	function testAndExpr() {
		$and1 = new \Query\AndExpr("abc", 'def');
		$and2 = new \Query\AndExpr("abc", 'def');
		$this->assertTrue($and1->equals($and2));
		$this->assertFalse($and1->equals(new \Query\AndExpr('a', 'd')));
		$exprList1 = new \Query\ExprList();
		$exprList2 = new \Query\ExprList();
		$exprList1->add($and1);
		$exprList1->add($and2);
		$this->assertFalse($exprList1->equals($exprList2));
		$exprList2->add($and1);
		$exprList2->add($and2);
		$this->assertTrue($exprList1->equals($exprList2));
	}
	function testQueryBuilder() {
		// query template, probably loaded from external file
		$template = "select emp.* from employee emp" .
			" join departments dep on emp.id_department = dep.id" .
			" @{where}" .
			" @{order}" .
			" limit :limit offset :offset";
		// create "where" clause
		$where = \Query\Expressions::where()
			->andExpr("emp.surname = :surname")
			->andExpr("emp.name like :name")
			->andExpr(
				\Query\Expressions::orExpr(
					\Query\Expressions::expr("emp.salary > :salary")->andExpr("emp.position in (:positionList)"),
					\Query\Expressions::not("emp.age > :ageThreshold")
				)
			)
        	->andExpr("status != 'ARCHIVED'");
		// create "order" clause
		$order = \Query\Expressions::orderBy()->add("dep.id desc")->add("cust.salary");
		// create builder from template and fill clauses
		$sql = \Query\QueryBuilder::query($template)
			->set("where", $where)
			->set("order", $order)
			->build();

$sqlResult = <<<STRBLOCK
select emp.* from employee emp
 join departments dep on emp.id_department = dep.id
 where emp.surname = :surname
 and emp.name like :name
 and ((emp.salary > :salary and emp.position in (:positionList)) or (not (emp.age > :ageThreshold)))
 and status != 'ARCHIVED'
 order by dep.id desc, cust.salary
 limit :limit offset :offset
STRBLOCK;

		$this->assertEquals(str_replace("\n", "", $sqlResult), $sql);
	}

	function testMysqlQueryBuilder() {
		$expr = \Query\Expressions::where()
			->andExpr(new \Query\EqualExpr("surname", "surname"))
			->andExpr(new \Query\LikeExpr("name", "name"))
			->andExpr(
				\Query\Expressions::orExpr(
					\Query\Expressions::expr(new \Query\GreaterThanExpr("salary", "5000"))
						->andExpr(new \Query\InExpr("position", "1,2,3"))
						->andExpr(new \Query\InExpr("position", array(1,2.6,"3", "a"))),
					\Query\Expressions::not(new \Query\GreaterThanExpr("age", "`ageThreshold`"))
				)
			)
			->andExpr(new \Query\NotEqualExpr("status", "ARCHIVED"));

		$columns = array(
			"surname" => "prefix_surname",
			"name" => "prefix_name",
		);
		$sql = \Query\MysqlQueryBuilder::query($expr, $columns)->build();
		$sqlResult = <<<STRBLOCK
where prefix_surname = 'surname'
 and prefix_name like 'name'
 and ((salary > 5000 and position in (1,2,3) and position in (1,2.6,3,'a')) or (not (age > `ageThreshold`)))
 and status != 'ARCHIVED'
STRBLOCK;

		$this->assertEquals(str_replace("\n", "", $sqlResult), $sql);
	}
}
