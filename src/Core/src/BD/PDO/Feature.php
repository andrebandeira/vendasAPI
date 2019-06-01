<?php

namespace Core\BD\PDO;

use Core\BD\Model;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\TableGateway\Feature\AbstractFeature;

class Feature extends AbstractFeature
{
    private $_model;

    public function __construct(Model $model)
    {
        $this->_model = $model;
    }

    public function postInsert(
        StatementInterface $statement, ResultInterface $result
    ) {
        $this->_model->setData($result->current());
    }

    public function postUpdate(
        StatementInterface $statement, ResultInterface $result
    ) {
        $this->_model->setData($result->current());
    }
}