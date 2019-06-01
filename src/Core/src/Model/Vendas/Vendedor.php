<?php

namespace Core\Model\Vendas;

use Core\BD\Model;


class Vendedor extends Model
{
    public function initialize()
    {
        $this->connection = 'Vendas';
        $this->table = 'VENDEDOR';
        $this->PK = ['ID'];
        $this->SK = ['EMAIL'];
    }
}