<?php


namespace Core\Repository\Vendas;


use Core\BD\Repository;

class Venda extends Repository
{
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

        if ($valor) {
            if (!is_numeric($valor)) {
                $valor = str_replace(".", "", $valor);
                $valor = str_replace(",", ".", $valor);
            }
            $valor = number_format($valor, 2, ".", "");
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

        if ($valor) {
            if (!is_numeric($valor)) {
                $valor = str_replace(".", "", $valor);
                $valor = str_replace(",", ".", $valor);
            }
            $valor = number_format($valor, 2, ".", "");
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

    public static function buscaVenda(
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


        $venda = self::getModel();

        $qry = "select 	venda.id id,
                        vendedor.nome nome,
                        vendedor.email email,
                        venda.comissao comissao,
                        venda.valor valor,
                        venda.data_hora data_hora
                from venda join vendedor on vendedor.id = venda.vendedor
                :FILTRO_VENDA 
                :FILTRO_DATAINICIO
                :FILTRO_DATAFIM
                order by 1 asc";

        $params = [];

        if ($id) {
            $qry = str_replace(
                ':FILTRO_VENDA',
                'where venda.id = :PAR_VENDA',
                $qry
            );

            $params['PAR_VENDA'] = $id;
        } else {
            $qry = str_replace(
                ':FILTRO_VENDA',
                '',
                $qry
            );
        }

        if ($dataInicio) {
            if (strpos($qry, 'where') !== false) {
                $qry = str_replace(
                    ':FILTRO_DATAINICIO',
                    'and cast(venda.data_hora as date) >= :PAR_DTINICIO',
                    $qry
                );
            } else {
                $qry = str_replace(
                    ':FILTRO_DATAINICIO',
                    'where cast(venda.data_hora as date) >= :PAR_DTINICIO',
                    $qry
                );
            }

            $params['PAR_DTINICIO'] = $dataInicio;
        } else {
            $qry = str_replace(
                ':FILTRO_DATAINICIO',
                '',
                $qry
            );
        }

        if ($dataFim) {
            if (strpos($qry, 'where') !== false) {
                $qry = str_replace(
                    ':FILTRO_DATAFIM',
                    'and cast(venda.data_hora as date) <= :PAR_DTFIM',
                    $qry
                );
            } else {
                $qry = str_replace(
                    ':FILTRO_DATAFIM',
                    'where cast(venda.data_hora as date) <= :PAR_DTFIM',
                    $qry
                );
            }

            $params['PAR_DTFIM'] = $dataFim;
        } else {
            $qry = str_replace(
                ':FILTRO_DATAFIM',
                '',
                $qry
            );
        }

        return $venda->execute($qry, $params);
    }

    public static function findByVendedor(
        string $vendedor = null,
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


        $venda = self::getModel();

        $qry = "select 	venda.id id,
                        vendedor.nome nome,
                        vendedor.email email,
                        venda.comissao comissao,
                        venda.valor valor,
                        venda.data_hora data_hora
                from venda join vendedor on vendedor.id = venda.vendedor
                :FILTRO_VENDEDOR 
                :FILTRO_DATAINICIO
                :FILTRO_DATAFIM
                order by 1 asc";

        $params = [];

        if ($vendedor) {
            $qry = str_replace(
                ':FILTRO_VENDEDOR',
                'where vendedor.id = :PAR_VENDEDOR',
                $qry
            );

            $params['PAR_VENDEDOR'] = $vendedor;
        } else {
            $qry = str_replace(
                ':FILTRO_VENDEDOR',
                '',
                $qry
            );
        }

        if ($dataInicio) {
            if (strpos($qry, 'where') !== false) {
                $qry = str_replace(
                    ':FILTRO_DATAINICIO',
                    'and cast(venda.data_hora as date) >= :PAR_DTINICIO',
                    $qry
                );
            } else {
                $qry = str_replace(
                    ':FILTRO_DATAINICIO',
                    'where cast(venda.data_hora as date) >= :PAR_DTINICIO',
                    $qry
                );
            }

            $params['PAR_DTINICIO'] = $dataInicio;
        } else {
            $qry = str_replace(
                ':FILTRO_DATAINICIO',
                '',
                $qry
            );
        }

        if ($dataFim) {
            if (strpos($qry, 'where') !== false) {
                $qry = str_replace(
                    ':FILTRO_DATAFIM',
                    'and cast(venda.data_hora as date) <= :PAR_DTFIM',
                    $qry
                );
            } else {
                $qry = str_replace(
                    ':FILTRO_DATAFIM',
                    'where cast(venda.data_hora as date) <= :PAR_DTFIM',
                    $qry
                );
            }

            $params['PAR_DTFIM'] = $dataFim;
        } else {
            $qry = str_replace(
                ':FILTRO_DATAFIM',
                '',
                $qry
            );
        }

        return $venda->execute($qry, $params);
    }

    public static function valorVendedor(){
        $venda = self::getModel();

        $qry = "select 	vendedor.email email,
                        sum(venda.valor) valor
                from venda join vendedor on vendedor.id = venda.vendedor
                group by 1
                order by 2 desc";

        return $venda->execute($qry);
    }

    public static function valorDia(){
        $venda = self::getModel();

        $qry = "select 	cast(venda.data_hora as date) dia,
                        sum(venda.valor) valor
                from venda join vendedor on vendedor.id = venda.vendedor
                group by 1
                order by 1 asc";

        return $venda->execute($qry);
    }

    public static function comissaoVendedor(){
        $venda = self::getModel();

        $qry = "select 	vendedor.email email,
                        sum(venda.comissao) comissao
                from venda join vendedor on vendedor.id = venda.vendedor
                group by 1
                order by 2 desc";

        return $venda->execute($qry);
    }

    public static function comissaoDia(){
        $venda = self::getModel();

        $qry = "select 	cast(venda.data_hora as date) dia,
                        sum(venda.comissao) comissao
                from venda join vendedor on vendedor.id = venda.vendedor
                group by 1
                order by 1 asc";

        return $venda->execute($qry);
    }
}