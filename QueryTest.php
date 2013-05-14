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

	function testOperatorExpr() {
		$expr = \Query\Expressions::expr(new \Query\EqualExpr("surname", "myname"));
		$dict = array();
		$dict['surname'] = 'myname';

		$this->assertEquals($expr->evaluate($dict), true);
		$this->assertEquals("surname = 'myname'", "{$expr}");

		$expr1 = $expr->andExpr(new \Query\EqualExpr("surname", "yourname"));
		$this->assertEquals("surname = 'myname' and surname = 'yourname'", "{$expr1}");
		$this->assertEquals($expr1->evaluate($dict), false);

		$expr2 = $expr->andExpr(new \Query\NotEqualExpr("surname", "yourname"));
		$this->assertEquals("surname = 'myname' and surname != 'yourname'", "{$expr2}");
		$this->assertEquals($expr2->evaluate($dict), true);

		$dict['salary'] = 6000;
		$expr3 = $expr2->andExpr(new \Query\GreaterThanExpr("salary", "5000"));
		$this->assertEquals("surname = 'myname' and surname != 'yourname' and salary > 5000", "{$expr3}");
		$this->assertEquals($expr3->evaluate($dict), true);

		$expr4 = $expr2->andExpr(new \Query\LessThanExpr("salary", "5000"));
		$this->assertEquals("surname = 'myname' and surname != 'yourname' and salary < 5000", "{$expr4}");
		$this->assertEquals($expr4->evaluate($dict), false);

		$dict['position'] = 2;
		$dict['age'] = 25;
		$dict['ageThreshold'] = 45;
		$expr5 = \Query\Expressions::expr(new \Query\EqualExpr("surname", "myname"))
			->andExpr(new \Query\NotEqualExpr("surname", "yourname"))
			->andExpr(new \Query\GreaterThanExpr("salary", "5000"))
			->andExpr(new \Query\LessThanExpr("salary", "7000"))
			->andExpr(new \Query\OrExpr(
					new \Query\InExpr("position", "1,2,3"),
					new \Query\NotExpr(
						new \Query\InExpr("position", array(5, 6, 7))
					)
				)
			)
			->andExpr(\Query\Expressions::not(new \Query\GreaterThanExpr("age", "`ageThreshold`")));

		$this->assertEquals("surname = 'myname' and surname != 'yourname' and salary > 5000 and salary < 7000 and ((position in (1,2,3)) or (not (position in (5,6,7)))) and not (age > `ageThreshold`)", "{$expr5}");
		$this->assertEquals($expr5->evaluate($dict), true);

		$dict['age'] = 50;
		$this->assertEquals($expr5->evaluate($dict), false);

		$dict['age'] = 30;
		$dict['salary'] = 8000;
		$this->assertEquals($expr5->evaluate($dict), false);

		$dict['age'] = 30;
		$dict['salary'] = 3000;
		$this->assertEquals($expr5->evaluate($dict), false);

		$dict['salary'] = 6000;
		$this->assertEquals($expr5->evaluate($dict), true);

		$dict['ageThreshold'] = 28;
		$this->assertEquals($expr5->evaluate($dict), false);

		$dict['age'] = 20;
		$dict['position'] = 8;
		$this->assertEquals($expr5->evaluate($dict), true);
		$dict['position'] = 7;
		$this->assertEquals($expr5->evaluate($dict), false);

	}
}
