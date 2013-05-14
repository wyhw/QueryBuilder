Small library for building SQL query strings
============================================

####Samples
````
// query template, probably loaded from external file 
$template = "select emp.* from employee emp" . 
	" join departments dep on emp.id_department = dep.id" . 
	" @{where}" . 
	" @{order}" . 
	" limit :limit offset :offset";
// create "where" clause
$where = \Query\Expressions::where()
	-&gt;andExpr("emp.surname = :surname")
	-&gt;andExpr("emp.name like :name")
	-&gt;andExpr(
		\Query\Expressions::orExpr(
			\Query\Expressions::expr("emp.salary &lt; :salary")-&gt;andExpr("emp.position in (:positionList)"),
			\Query\Expressions::not("emp.age &lt; :ageThreshold")
		)
	)
	-&gt;andExpr("status != 'ARCHIVED'");
// create "order" clause
$order = \Query\Expressions::orderBy()-&gt;add("dep.id desc")-&gt;add("cust.salary");
// create builder from template and fill clauses
$sql = \Query\QueryBuilder::query($template)
	-&gt;set("where", $where)
	-&gt;set("order", $order)
	-&gt;build();
	
	

//equal, notequal, greaterthan, lessthan, in, like

$expr = \Query\Expressions::where()
			->andExpr(new \Query\EqualExpr("surname", "surname"))
			->andExpr(new \Query\LikeExpr("name", "name"))
			->andExpr(
				\Query\Expressions::orExpr(
					\Query\Expressions::expr(new \Query\LessThanExpr("salary", "5000"))
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
		$sql = \Query\MysqlQueryBuilder::query($expr, $columns)-&gt;build();
		$sqlResult = &gt;&gt;&gt;STRBLOCK
where prefix_surname = 'surname'
 and prefix_name like 'name'
 and ((salary &lt; 5000 and position in (1,2,3) and position in (1,2.6,3,'a')) or (not (age &gt; `ageThreshold`)))
 and status != 'ARCHIVED'
STRBLOCK;

//operator : evaluate
$expr = \Query\Expressions::expr(new \Query\EqualExpr("surname", "myname"));
$dict = array();
$dict['surname'] = 'myname';
$expr->evaluate($dict);

$expr1 = $expr->andExpr(new \Query\EqualExpr("surname", "yourname"));
$expr->evaluate($dict);
$expr2 = $expr->andExpr(new \Query\NotEqualExpr("surname", "yourname"));
$expr2->evaluate($dict);
$dict['salary'] = 6000;
$expr3 = $expr2->andExpr(new \Query\GreaterThanExpr("salary", "5000"));
$expr3->evaluate($dict);
$expr4 = $expr2->andExpr(new \Query\LessThanExpr("salary", "5000"));
$expr4->evaluate($dict);

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
$expr5->evaluate($dict);

请参见测试用例：QueryTest.php

````
参考：[java query-string-builder](https://github.com/alexkasko/query-string-builder).		
