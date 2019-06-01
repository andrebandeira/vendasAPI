<?php

namespace Core\BD\PDO;


use Core\BD\Model;
use Zend\Stdlib\ArrayObject;

class ResultSet extends \Zend\Db\ResultSet\ResultSet
{
    public function __construct($returnType = self::TYPE_ARRAYOBJECT, $arrayObjectPrototype = null)
    {
        parent::__construct($returnType, $arrayObjectPrototype);
    }

    /**
     * @return Model
     */
    public function current()
    {
        if (!$this->getDataSource()) {
            return null;
        }

        $data = parent::current();

        if (!$data) {
            return $data;
        }

        $data = $data->getModel();

        if ($this->returnType === self::TYPE_ARRAYOBJECT && is_array($data)) {
            /** @var $ao ArrayObject */
            $ao = clone $this->arrayObjectPrototype;
            if ($ao instanceof ArrayObject || method_exists($ao, 'exchangeArray')) {
                $ao->exchangeArray($data);
            }
            return $ao;
        }

        return $data;
    }

    public function count()
    {
        // Realiza o buffer dos dados
        $this->buffer();

        // Armazena quantos registro possui o array
        $this->count = count($this->toArray());

        // Retorna para o primeiro elemento
        $this->rewind();

        // Retorna a quantidade de dados
        return $this->count;
    }

    public function toArray()
    {
        $return = [];
        foreach ($this as $row) {
            $return[] = $row;
        }
        return $return;
    }
}