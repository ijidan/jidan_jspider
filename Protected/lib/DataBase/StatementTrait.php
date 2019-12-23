<?php

namespace Lib\DataBase;

/**
 * Class StatementTrait
 * @package Lib\DataBase
 */
trait StatementTrait
{
	/**
	 * @param string $where
	 */
	public function setConditionWhere($where=""){
		$this->whereClause=" where ".$where;
	}

	/**
	 * @param array $values
	 */
	public function setConditionValues(array  $values=[]){
		$this->values=$values;
	}
}
