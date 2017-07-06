<?php 
namespace BrezgalovQueryBuilder;

use Traits\KnowJoinTypes;
use Exceptions\UnexpectedStatmentException;

class Statement {
	use KnowJoinTypes;
	use KnowOrderTypes;

	private $query;
	private $queryTail;

	private $whereQuery;
	private $joinQuery;

	private $select;
	private $from;
	private $where;
	private $groupBy;
	private $orderBy;
	private $limit;

	protected function validateSelectAndFrom($statement){
		if (!$this->select || !$this->from) {
			throw new UnexpectedStatmentException($statement . ' statement should be preceded by SELECT and FROM');
		}
	}

	public function __construct() {
		$this->select = false;
		$this->from = false;
		$this->groupBy = false;
		$this->orderBy = false;
		$this->limit = false;
	}

	public function getSql() {
		if ($this->select) {
			$query = $this->query;
			$query .= $this->joinQuery;
			$query .= $this->whereQuery;
			$query .= $this->queryTail;
			return $query;
		}
	}

	public function select($fields) {
		$this->query = 'SELECT ' . $fields;
		$this->whereQuery = '';
		$this->joinQuery = '';
		$this->queryTail = '';
		$this->select = true;
	}

	public function from($fromStatement) {
		// if (!$this->select) {
		// 	throw new UnexpectedStatmentException('FROM statement should be preceded by SELECT');
		// }

		$this->query .= ' FROM '.$fromStatement;
		$this->from = true;
	}

	public function where($statement, $or) {
		// $this-validateSelectAndFrom('WHERE');

		$wherePrefix = ' WHERE ';
		if ($this->where) {
			$wherePrefix = ()? ' OR ' : ' AND ';
		}
		$this->whereQuery .= $wherePrefix . $statement;
	}

	public function join($statement, $type = '') {
		// $this-validateSelectAndFrom('JOIN');
		// if ($this->where)
		
		$this->joinQuery .= ' ' . $type . ' JOIN ' . $statement;
	}

	public function groupBy($statement) {
		// $this-validateSelectAndFrom();
		// if ($this->orderBy || $this->limit) {
		// 	throw new UnexpectedStatmentException('GROUP BY statement should not follow nor ORDER BY nor LIMIT');
		// }

		$this->queryTail .= ' GROUP BY '.$statement;
		$this->groupBy = true;
	}

	public function orderBy($statement, $type) {
		// $this-validateSelectAndFrom();
		// if ($this->limit) {
		// 	throw new UnexpectedStatmentException('ORDER BY statement should not follow LIMIT');
		// }

		$this->queryTail .= 'ORDER BY ' . $statement . ' ' . $type;
		$this->orderBy = true;
	}

	public function limit($limit, $offset) {
		// $this-validateSelectAndFrom();
		$this->queryTail .= 'LIMIT ' . $offset . ',' . $limit;
	}
}