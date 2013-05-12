Small library for building SQL query strings use PHP

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

参考：[java query-string-builder](https://github.com/alexkasko/query-string-builder).		