<?php

/**
 * @license MIT
 * @license http://opensource.org/licenses/MIT
 */

namespace Lib\DataBase;

use Slim\PDO\Statement\InsertStatement;

/**
 * Class InsertStatement.
 *
 * @author Fabian de Laender <fabian@faapz.nl>
 */
class BaseInsertStatement extends InsertStatement
{
    use StatementTrait;

    /**
     * BaseInsertStatement constructor.
     * @param BaseDatabase $dbh
     * @param array $columnsOrPairs
     */
    public function __construct(BaseDatabase $dbh, array $columnsOrPairs)
    {
        parent::__construct($dbh,$columnsOrPairs);
    }
}
