<?php

namespace Core\Repository\Vendas;

use Core\BD\Repository;

class Vendedor extends Repository
{
    public static function insert(
        string $nome  = null,
        string $email = null,
        string $senha = null
    ){
        $vendedor = self::getModel();

        $vendedor->NOME = $nome;
        $vendedor->EMAIL = $email;
        $vendedor->SENHA = password_hash($senha, PASSWORD_BCRYPT);

        $vendedor->insert();

        return $vendedor;
    }

    public static function update(
        string $id,
        string $nome = null,
        string $email = null,
        string $senha = null
    ){
        $vendedor = self::getModel();

        $vendedor->ID     = $id;
        $vendedor->NOME  = $nome;
        $vendedor->EMAIL = $email;
        $vendedor->SENHA = password_hash($senha, PASSWORD_BCRYPT);

        $vendedor->update();

        return $vendedor;
    }

    public static function delete(string $id)
    {
        $vendedor = self::getModel();
        $vendedor->ID = $id;

        $vendedor->delete();
    }
}