# SqlQuery
This is a simple tool I made for myself to help with building complex sql select queries.
The main idea of it is to create a select query-string as fast and simple is posible, to provide both perfomance and ease of use.
This tool also can create Insert, Update and Delete queries just for functional fullness.
## How to use
### Initialistaion
```php
$sqlQuery = new SqlQuery();
```
### Select statements
Pretty simple select statement
```php
$query = $sqlQuery->select('name, id')->from('users')->where("id >= '2'")->getSql();
//Result: "SELECT name, id FROM users WHERE id >= '2'"
```
More complex statement. 'WHERE' and 'WHERE IN' clause accepts optional bool parameter turning it from 'AND' to 'OR'.
It also shows 'whereIn' clause, that turns array of values into 'IN' statement, escaping it with single quote.
```php
$query = $sqlQuery->select('name, id')
	->from('users')
	->where("email LIKE '%@example.com%'")
	->whereIN('id', ['4', '8', '15', '16', '23', '42'], true) 
	->getSql();
//Result: "SELECT name, id FROM users WHERE email LIKE '%@example.com%' OR id IN ('4', '8', '15', '16', '23', '42')"
```
### Statement feature
When building a complex 'WHERE' statement it may took a large amount of code to implement statement correctly. Thats why I've added a Statement class, that allows to store conditions and output them in formated way.
To add a condition tou should provide condition itself, do you whant to overwrite previous condition if exists and condition name.
Dollar signs should wrap condition name in format string.
```php
$whereStatement = new Statement();
$where = $whereStatement->addCondition("id >= '2'", false, 'i2')
	->addCondition("id <= '4'", false, 'i4')
	->addCondition("id >= '8'", false, 'i8')
	->addCondition("id < '1024'", false, 'i1024')
	->build('($i2$ AND $i4$) OR ($i8$ AND $i1024$)');
```
After creating statement it can be easily used to build a query:
```php
$query = $sqlQuery->select('name, id')->from('users')->where($where)->getSql();
//Result: "SELECT name, id FROM users WHERE ( id >= '2'  AND  id <= '4' ) OR ( id >= '8'  AND  id < '1024' )"
```
### Insert statements
Insert statement arguments are table name, array of fields name (which can be empty) and array of arrays with values.
```php
$query = $sqlQuery->insert(
	'users', 
	['name', 'email'], 
	[
		['Jhoe', 'jhoe@example.com'],
		['Mark', 'mark@example.com'],
	]
)->getSql();
//Result: "INSERT INTO users (name,email) VALUES ('Jhoe', 'jhoe@example.com'),('Mark', 'mark@example.com')"
```
'INSERT FROM' statement is also available. 
```php
$queryFrom = $sqlQuery->select('name, email')
	->from('users')
	->getSql();
$query = $sqlQuery->insertFrom(
	'users', 
	['name', 'email'], 
	$queryFrom
)->getSql();
//Result: "INSERT INTO users (name,email) FROM (SELECT name, email FROM users)"
```
### Prepare IN feature
When you need to use an IN statementm you can also use method prepareIn:
```php
SqlQuery::prepareIn('id', ['4', '8', '15', '16', '23', '42'])
//Result: "id IN ('4', '8', '15', '16', '23', '42')"
```
### Update statements
In order to update the table you should specify its name, array formated like "field" => "value" and where condition.
```php
$sqlQuery = new SqlQuery();
$query = $sqlQuery->update(
	'users', 
	['email' => ''], 
	"name = 'Carl'"
)->getSql();	
//Result: "UPDATE users SET email = '' WHERE name = 'Carl'"

$sqlQuery = new SqlQuery();
$query = $sqlQuery->update(
	'users', 
	['email' => ''], 
	SqlQuery::prepareIn('id', ['4', '8', '15', '16', '23', '42'])
)->getSql();	
//Result: "UPDATE users SET email = '' WHERE id IN ('4', '8', '15', '16', '23', '42')"
```
### Delete statements
Delete query requires table name and where condition
```php
$query = $sqlQuery->delete("users", "email = ''")->getSql();
//Result: "DELETE FROM users WHERE email = ''"
```