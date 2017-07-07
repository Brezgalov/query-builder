<?php 
namespace BrezgalovQueryBuilder;

use Exceptions\UnexpectedStatmentException;

class SqlQuery {
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

	private $insert;

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
		} elseif ($this->insert) {
			return $this->query;
		}
	}

	public function select($fields) {
		$this->query = 'SELECT ' . $fields;
		$this->whereQuery = '';
		$this->joinQuery = '';
		$this->queryTail = '';
		$this->select = true;
		return $this;
	}

	public function from($fromStatement) {
		$this->query .= ' FROM '.$fromStatement;
		$this->from = true;
		return $this;
	}

	public function where($statement, $or = false) {
		$wherePrefix = ' WHERE ';
		if ($this->where) {
			$wherePrefix = ($or)? ' OR ' : ' AND ';
		}
		$this->whereQuery .= $wherePrefix . $statement;
		return $this;
	}

	public function join($statement, $type = '') {
		$this->joinQuery .= ' ' . $type . ' JOIN ' . $statement;
		return $this;
	}

	public function groupBy($statement) {
		$this->queryTail .= ' GROUP BY '.$statement;
		$this->groupBy = true;
		return $this;
	}

	public function orderBy($statement, $type) {
		$this->queryTail .= 'ORDER BY ' . $statement . ' ' . $type;
		$this->orderBy = true;
		return $this;
	}

	public function limit($limit, $offset) {
		$this->queryTail .= 'LIMIT ' . $offset . ',' . $limit;
		return $this;
	}

	public function insert($to, $fields, $values) {
		$this->query = 'INSERT INTO ' . $to;
		if (!empty($fields)) {
			$this->query .= '(' . implode(',', $fields) . ')';
		}
		$this->query .= ' VALUES ';
		$valuesStrings = [];
		foreach ($values as $value) {
			array_push(
				$valuesStrings, 
				'(' . implode(',', $value) . ')'
			);
		}
		$this->query .= implode(',', $valuesStrings);
		$this->insert = true;
		return $this;
	}

	public function insertFrom($to, $fields, $from) {
		$this->query = 'INSERT INTO ' . $to;
		if (!empty($fields)) {
			$this->query .= '( ' . implode(',', $fields) . ')';
		}
		$this->query .= $from;
	}
}