<?php


namespace Core\Repository\Vendas;


use Core\BD\Repository;

class Venda extends Repository
{
    public static function findByVendedor(string $vendedor = null) {
        $venda = self::getModel();
        $venda->VENDEDOR = $vendedor;
        return $venda->findAll();
    }

    public static function insert(
        string $vendedor = null,
        string $valor    = null,
        string $data     = null,
        string $comissao = null
    ){
        if (!$data) {
            $date = new \DateTime();
            $data = $date->format('Y-m-d H:i:s');
        }

        if (!$comissao) {
            $comissao = round($valor * 0.085, 2);
        }

        $venda = self::getModel();

        $venda->VENDEDOR = $vendedor;
        $venda->VALOR = $valor;
        $venda->DATA_HORA = $data;
        $venda->COMISSAO = $comissao;

        $venda->insert();

        return $venda;
    }

    public static function update(
        string $id,
        string $vendedor = null,
        string $valor    = null,
        string $data     = null,
        string $comissao = null
    ){
        if (!$data) {
            $date = new \DateTime();
            $data = $date->format('Y-m-d H:i:s');
        }

        if (!$comissao) {
            $comissao = round($valor * 0.085, 2);
        }

        $venda = self::getModel();

        $venda->ID     = $id;
        $venda->VENDEDOR = $vendedor;
        $venda->VALOR = $valor;
        $venda->DATA_HORA = $data;
        $venda->COMISSAO = $comissao;

        $venda->update();

        return $venda;
    }

    public static function delete(string $id)
    {
        $venda = self::getModel();
        $venda->ID = $id;

        $venda->delete();
    }
}