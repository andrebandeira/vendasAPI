<?php

namespace Core\Repository\Vendas;

use Core\BD\Repository;

class Vendedor extends Repository
{
    public static function insert(
        string $nome  = null,
        string $email = null
    ){
        $vendedor = self::getModel();

        $vendedor->NOME = $nome;
        $vendedor->EMAIL = $email;

        $vendedor->insert();

        return $vendedor;
    }

    public static function update(
        string $id,
        string $nome = null,
        string $email = null
    ){
        $vendedor = self::getModel();

        $vendedor->ID     = $id;
        $vendedor->NOME  = $nome;
        $vendedor->EMAIL = $email;

        $vendedor->update();

        return $vendedor;
    }

    public static function delete(string $id)
    {
        $vendedor = self::getModel();
        $vendedor->ID = $id;

        $vendedor->delete();
    }

    public static function buscaVendedor(
        string $id = null,
        string $dataInicio = null,
        string $dataFim = null
    ){
        if ($dataInicio) {
            $dataInicio = new \DateTime($dataInicio);
            $dataInicio = $dataInicio->format('Y-m-d');
        }

        if ($dataFim) {
            $dataFim = new \DateTime($dataFim);
            $dataFim = $dataFim->format('Y-m-d');
        }


        $vendedor = self::getModel();

        $qry = "select 	vendedor.id id,
                        vendedor.nome nome,
                        vendedor.email email,
                        sum(venda.comissao) comissao
                from vendedor left join venda on vendedor.id = venda.vendedor
                                                 :FILTRO_DATAINICIO
                                                 :FILTRO_DATAFIM
                :FILTRO_VENDEDOR                
                group by 1,2,3
                order by 1 asc";

        $params = [];

        if ($id) {
            $qry = str_replace(
                ':FILTRO_VENDEDOR',
                'where vendedor.id = :PAR_VENDEDOR',
                $qry
            );

            $params['PAR_VENDEDOR'] = $id;
        } else {
            $qry = str_replace(
                ':FILTRO_VENDEDOR',
                '',
                $qry
            );
        }

        if ($dataInicio) {
            $qry = str_replace(
                ':FILTRO_DATAINICIO',
                'and cast(venda.data_hora as date) >= :PAR_DTINICIO',
                $qry
            );

            $params['PAR_DTINICIO'] = $dataInicio;
        } else {
            $qry = str_replace(
                ':FILTRO_DATAINICIO',
                '',
                $qry
            );
        }

        if ($dataFim) {
            $qry = str_replace(
                ':FILTRO_DATAFIM',
                'and cast(venda.data_hora as date) <= :PAR_DTFIM',
                $qry
            );

            $params['PAR_DTFIM'] = $dataFim;
        } else {
            $qry = str_replace(
                ':FILTRO_DATAFIM',
                '',
                $qry
            );
        }

        return $vendedor->execute($qry, $params);
    }
}