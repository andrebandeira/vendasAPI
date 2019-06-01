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

    public static function find(Array $data){
        $model = new self();
        $model->setData($data);
        return $model->findOne();
    }

    public static function getAll(){
        $model = new self();
        return $model->all();
    }

    public static function findByVendedor(string $vendedor = null) {
        $venda = new self();
        $venda->VENDEDOR = $vendedor;
        return $venda->findAll();
    }

    public static function insertVenda(
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

        $venda = new self();

        $venda->VENDEDOR = $vendedor;
        $venda->VALOR = $valor;
        $venda->DATA_HORA = $data;
        $venda->COMISSAO = $comissao;

        $venda->insert();

        return $venda;
    }

    public static function updateVenda(
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

        $venda = new self();

        $venda->ID     = $id;
        $venda->VENDEDOR = $vendedor;
        $venda->VALOR = $valor;
        $venda->DATA_HORA = $data;
        $venda->COMISSAO = $comissao;

        $venda->update();

        return $venda;
    }

    public static function deleteVenda(string $id)
    {
        $venda     = new self();
        $venda->ID = $id;

        $venda->delete();
    }

    public function VENDEDOR_NOME() {
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

    public function VENDEDOR_EMAIL() {
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