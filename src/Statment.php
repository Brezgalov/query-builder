<?php 
namespace BrezgalovQueryBuilder;

use BrezgalovQueryBuilder\Exceptions\OverwritingConditionException;

class Statement {
	private $conditions;

	public function __construct() {
		$this->conditions = [
			'default' => '',
		];
	}

	public function addCondition($condition, $overwrite = false, $name = 'default') {
		if (!$overwrite && isset($this->conditions[$name])) {
			throw new OverwritingConditionException();
		}
		$this->conditions[$name] = $condition;
		return $this;
	}

	public function removeCondition($conditionName) {
		if ($conditionName == 'default') {
			$this->conditions['default'] = '';
		}
		else {
			unset($this->conditions[$conditionName]);
		}
		return $this;
	}

	public function build($format) {
		$parts = explode(' ', $format);
		$build = [];
		while (!empty($parts)) {
			$next = array_shift($parts);
			if ($next[0] == '$') {
				str_replace($next, '$', '');
				if (isset($this->conditions['$next'])) {
					array_push($build, $this->conditions['$next']);
					continue;
				}
			} 
			array_push($build, $next);
		}
		return implode(' ', $build);
	}
}