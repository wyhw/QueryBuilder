Small library for building SQL query strings
============================================

####Samples
<pre>
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

请参见测试用例：QueryTest.php

</pre>
参考：[java query-string-builder](https://github.com/alexkasko/query-string-builder).		
