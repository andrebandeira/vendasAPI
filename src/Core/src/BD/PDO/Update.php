<?php

namespace Core\BD\PDO;


use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Pdo\Pdo;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Exception\InvalidArgumentException;

class Update extends \Zend\Db\Sql\Update
{
    const SPECIFICATION_RETURNING = 'returning';

    private $returning;

    public function __construct($table = null)
    {
        $this->returning = [];
        $this->specifications = [
            self::SPECIFICATION_UPDATE => 'UPDATE %1$s',
            self::SPECIFICATION_JOIN => [
                '%1$s' => [
                    [3 => '%1$s JOIN %2$s ON %3$s', 'combinedby' => ' ']
                ]
            ],
            self::SPECIFICATION_SET => 'SET %1$s',
            self::SPECIFICATION_WHERE => 'WHERE %1$s',
            self::SPECIFICATION_RETURNING => 'RETURNING %1$s',
        ];
        parent::__construct($table);
    }

    protected function processReturning(
        PlatformInterface $platform,
        DriverInterface $driver = null,
        ParameterContainer $parameterContainer = null
    ) {
        $returning = $this->returning;

        return sprintf(
            $this->specifications[static::SPECIFICATION_RETURNING],
            implode(', ', $returning)
        );
    }

    public function returning($fields)
    {
        $this->returning = $fields;
    }
}