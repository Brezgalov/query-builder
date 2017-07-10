<?php 
namespace BrezgalovQueryBuilder;

use Exceptions\UnexpectedStatementException;

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
	private $update;
	private $delete;

	private function clear() {
		$this->query = '';
		$this->whereQuery = '';
		$this->joinQuery = '';
		$this->queryTail = '';

		$this->select = false;
		$this->insert = false;
		$this->update = false;
		$this->delete = false;
		$this->from = false;
		$this->groupBy = false;
		$this->orderBy = false;
		$this->limit = false;
	}

	public static function prepareIn($field, $values) {
		return $field . ' IN (' . implode(', ', $values) .')';
	}

	public function getSql() {
		if ($this->select) {
			$query = $this->query;
			$query .= $this->joinQuery;
			$query .= $this->whereQuery;
			$query .= $this->queryTail;
			return $query;
		} elseif ($this->insert || $this->update || $this->delete) {
			return $this->query;
		} else {
			return '';
		}
	}

	public function select($fields) {
		$this->clear();
		$this->query = 'SELECT ' . $fields;
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

	public function whereIn($field, $values, $or = false) {
		$statement = $field . ' IN (' . implode(', ', $values) .')';
		$this->where($statement, $or);
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
		$this->clear();
		$this->query = 'INSERT INTO ' . $to;
		if (!empty($fields)) {
			$this->query .= '(' . implode(',', $fields) . ')';
		}
		$this->query .= ' VALUES ';
		$valuesStrings = [];
		foreach ($values as $value) {
			if (!is_array($value)) {
				throw new \InvalidArgumentException('Values expected to be an array of arrays');
			}
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
			$this->query .= ' (' . implode(',', $fields) . ')';
		}
		$this->query .= ' FROM (' . $from . ')';
		$this->insert = true;
		return $this;
	}

	public function update($table, $values, $where='') {
		$this->clear();
		$this->query = 'UPDATE ' . $table . ' SET ';
		$keys = array_keys($values);
		$first = array_shift($keys);
		$this->query .= "`" . $first . "` = '" . ((string)$values[$first]) . "'";
		while (!empty($keys)) {
			$next = array_shift($keys);
			$this->query .= ", `" . $next . "` = '" . ((string)$values[$next]) . "'";
		}
		if ($where) {
			$this->query .= ' WHERE ' . $where;
		}
		return $this;
	}

	public function delete($fromTable, $where) {
		$this->clear();
		$this->query = 'DELETE FROM ' . $fromTable . ' WHERE ' . $where;
		return $this;
	}
}