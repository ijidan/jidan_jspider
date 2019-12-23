<?php

namespace Lib\DataBase;

use Slim\PDO\Statement\UpdateStatement;

/**
 * Class BaseUpdateStatement
 * @package Lib\DataBase
 */
class BaseUpdateStatement extends UpdateStatement
{
    use StatementTrait;

    /**
     * BaseUpdateStatement constructor.
     * @param BaseDatabase $dbh
     * @param array $pairs
     */
    public function __construct(BaseDatabase $dbh, array $pairs)
    {
        parent::__construct($dbh,$pairs);
    }
}
