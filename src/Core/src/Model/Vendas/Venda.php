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

    public function VENDEDOR_NOME()
    {
        $nome = null;

        $model = new Vendedor();

        $references = [
            'VENDEDOR' => 'ID'
        ];

        $relation = $this->getRelationship($model, $references);

        if ($relation) {
            $nome = $relation->NOME;
        }

        $this->VENDEDOR_NOME = $nome;

        return $nome;
    }

    public function VENDEDOR_EMAIL()
    {
        $email = null;

        $model = new Vendedor();

        $references = [
            'VENDEDOR' => 'ID'
        ];

        $relation = $this->getRelationship($model, $references);

        if ($relation) {
            $email = $relation->EMAIL;
        }

        $this->VENDEDOR_EMAIL = $email;

        return $email;
    }
}