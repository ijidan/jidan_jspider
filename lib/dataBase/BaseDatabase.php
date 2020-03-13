<?php

namespace Lib\DataBase;

use Slim\PDO\Database;
use Slim\PDO\Statement\SelectStatement;
use Slim\PDO\Statement\InsertStatement;
use Slim\PDO\Statement\UpdateStatement;
use Slim\PDO\Statement\DeleteStatement;

/**
 * Class Database.
 *
 * @author Fabian de Laender <fabian@faapz.nl>
 */
class BaseDatabase extends Database
{

    /**
     * @param array $columns
     *
     * @return SelectStatement
     */
    public function select(array $columns = array('*'))
    {
        return new BaseSelectStatement($this,$columns);
    }

    /**
     * @param array $columnsOrPairs
     *
     * @return InsertStatement
     */
    public function insert(array $columnsOrPairs = array())
    {
        return new BaseInsertStatement($this, $columnsOrPairs);
    }

    /**
     * @param array $pairs
     *
     * @return UpdateStatement
     */
    public function update(array $pairs = array())
    {
        return new BaseUpdateStatement($this, $pairs);
    }

    /**
     * @param null $table
     *
     * @return DeleteStatement
     */
    public function delete($table = null)
    {
        return new BaseDeleteStatement($this, $table);
    }
}
