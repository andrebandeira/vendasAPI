<?php

namespace Core\BD\PDO;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Exception\InvalidArgumentException;
use Zend\Db\Sql\Platform\AbstractPlatform;

class Sql extends \Zend\Db\Sql\Sql
{
    private $_returning;

    public function __construct(AdapterInterface $adapter, $table = null, AbstractPlatform $sqlPlatform = null)
    {
        parent::__construct($adapter, $table, $sqlPlatform);
    }

    public function insert($table = null)
    {
        if ($this->table !== null && $table !== null) {
            throw new InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }

        $insert = new Insert(($table) ?: $this->table);
        $insert->returning($this->_returning);

        return $insert;
    }

    public function update($table = null)
    {
        if ($this->table !== null && $table !== null) {
            throw new InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }

        $update = new Update(($table) ?: $this->table);
        $update->returning($this->_returning);

        return $update;
    }

    public function setReturning(array $returning){
        $this->_returning = $returning;
    }
}