<?php

namespace Lib\DataBase;

use Slim\PDO\Statement\SelectStatement;


/**
 * Class BaseSelectStatement
 * @package Lim\DataBase
 */
class BaseSelectStatement extends SelectStatement
{
	use StatementTrait;
	/**
	 * BaseSelectStatement constructor.
	 * @param BaseDatabase $dbh
	 * @param array $columns
	 */
    public function __construct(BaseDatabase $dbh, array $columns)
    {
        parent::__construct($dbh,$columns);
    }
}
