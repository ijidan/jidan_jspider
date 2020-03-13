<?php


namespace Lib\DataBase;

use Slim\PDO\Statement\DeleteStatement;

/**
 * Class DeleteStatement.
 *
 * @author Fabian de Laender <fabian@faapz.nl>
 */
class BaseDeleteStatement extends DeleteStatement
{
	use StatementTrait;

	/**
	 * BaseDeleteStatement constructor.
	 * @param BaseDatabase $dbh
	 * @param $table
	 */
    public function __construct(BaseDatabase $dbh, $table)
    {
	    parent::__construct($dbh,$table);
    }

}
