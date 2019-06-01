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

    public static function find(Array $data){
        $model = new self();
        $model->setData($data);
        return $model->findOne();
    }

    public static function getAll(){
        $model = new self();
        return $model->all();
    }

    public static function insertVendedor(string $nome = null, string $email  = null, string $senha = null)
    {
        $vendedor = new self();

        $vendedor->NOME = $nome;
        $vendedor->EMAIL = $email;
        $vendedor->SENHA = password_hash($senha, PASSWORD_BCRYPT);

        $vendedor->insert();

        return $vendedor;
    }

    public static function updateVendedor(string $id, string $nome = null, string $email = null, string $senha = null)
    {
        $vendedor = new self();

        $vendedor->ID     = $id;
        $vendedor->NOME  = $nome;
        $vendedor->EMAIL = $email;
        $vendedor->SENHA = password_hash($senha, PASSWORD_BCRYPT);

        $vendedor->update();

        return $vendedor;
    }

    public static function deleteVendedor(string $id)
    {
        $vendedor     = new self();
        $vendedor->ID = $id;

        $vendedor->delete();
    }
}