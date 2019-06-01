<?php

namespace Core\Model\Vendas;

use Core\BD\Model;

class Usuario extends Model
{
    public function initialize()
    {
        $this->connection = 'Vendas';
        $this->table = 'USUARIO';
        $this->PK = ['ID'];
        $this->SK = ['EMAIL'];
    }

    public static function find(Array $data){
        $usuario = new self();
        $usuario->setData($data);
        return $usuario->findOne();
    }
}