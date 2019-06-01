<?php

namespace Core\Model\Vendas;

use Core\BD\Model;

class Config extends Model
{
    public function initialize()
    {
        $this->connection = 'Vendas';
        $this->table = 'CONFIG';
        $this->PK = ['ID'];
    }
}