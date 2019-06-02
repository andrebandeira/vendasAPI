<?php

namespace Core\Model\Vendas;

use Core\BD\Model;


class Venda extends Model
{
    public function initialize()
    {
        $this->connection = 'Vendas';
        $this->table = 'VENDA';
        $this->PK = ['ID'];
        $this->SK = ['VENDEDOR', 'DATA_HORA', 'VALOR'];
    }
}